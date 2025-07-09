<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\Absensi;
use App\Models\IzinSakit;
use App\Models\Cuti;
use App\Models\KategoriAbsensi;
use App\Models\StatusCuti;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceCheckService
{
    /**
     * Get karyawan and manajer employees who haven't checked in by a specific time
     * and don't have approved leave (izin sakit or cuti)
     *
     * @param string|null $date Date in Y-m-d format, defaults to today
     * @return Collection
     */
    public function getAbsentEmployees(?string $date = null): Collection
    {
        $date = $date ?? Carbon::now()->format('Y-m-d');
        
        // Check if it's weekend (Saturday = 6, Sunday = 0)
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        if ($dayOfWeek == 0 || $dayOfWeek == 6) {
            return collect(); // Return empty collection for weekends
        }
        
        // Get karyawan and manajer roles
        $karyawanRole = Role::where('code', 'R03')->first();
        $manajerRole = Role::where('code', 'R02')->first();
        
        $roleIds = [];
        if ($karyawanRole) $roleIds[] = $karyawanRole->id;
        if ($manajerRole) $roleIds[] = $manajerRole->id;
        
        if (empty($roleIds)) {
            return collect();
        }
        
        // Get all active users with karyawan or manajer role
        $employees = User::whereIn('role_id', $roleIds)
            ->where('is_active', 1)
            ->get();
        
        // Get users who have checked in on the specified date
        $checkedInUsers = Absensi::where('tanggal', $date)
            ->pluck('user_id')
            ->toArray();
        
        // Get approved status cuti (S02 = Disetujui)
        $approvedStatus = StatusCuti::where('kode', 'S02')->first();
        
        // Get users with approved izin sakit on the specified date
        $izinSakitUsers = IzinSakit::where('tanggal', $date)
            ->where('deleted_at', null)
            ->pluck('user_id')
            ->toArray();
        
        // Get users with approved cuti that covers the specified date
        $cutiUsers = collect();
        if ($approvedStatus) {
            $cutiUsers = Cuti::where('status_cuti_id', $approvedStatus->id)
                ->where('deleted_at', null)
                ->where('tanggal_mulai', '<=', $date)
                ->where('tanggal_akhir', '>=', $date)
                ->pluck('user_id');
        }
        
        // Combine all users with approved leave
        $approvedLeaveUsers = array_merge($izinSakitUsers, $cutiUsers->toArray());
        
        // Find users who haven't checked in and don't have approved leave
        $absentUsers = $employees->whereNotIn('id', $checkedInUsers)
            ->whereNotIn('id', $approvedLeaveUsers);
        
        return $absentUsers;
    }
    
    /**
     * Get attendance statistics for a specific date
     *
     * @param string|null $date Date in Y-m-d format, defaults to today
     * @return array
     */
    public function getAttendanceStats(?string $date = null): array
    {
        $date = $date ?? Carbon::now()->format('Y-m-d');
        
        // Check if it's weekend (Saturday = 6, Sunday = 0)
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        if ($dayOfWeek == 0 || $dayOfWeek == 6) {
            return [
                'total_employees' => 0,
                'checked_in' => 0,
                'absent' => 0,
                'on_leave' => 0,
                'attendance_rate' => 0,
                'is_weekend' => true
            ];
        }
        
        $karyawanRole = Role::where('code', 'R03')->first();
        $manajerRole = Role::where('code', 'R02')->first();
        
        $roleIds = [];
        if ($karyawanRole) $roleIds[] = $karyawanRole->id;
        if ($manajerRole) $roleIds[] = $manajerRole->id;
        
        if (empty($roleIds)) {
            return [
                'total_employees' => 0,
                'checked_in' => 0,
                'absent' => 0,
                'on_leave' => 0,
                'attendance_rate' => 0
            ];
        }
        
        $totalEmployees = User::whereIn('role_id', $roleIds)
            ->where('is_active', 1)
            ->count();
            
        $checkedIn = Absensi::where('tanggal', $date)
            ->whereHas('user', function($query) use ($roleIds) {
                $query->whereIn('role_id', $roleIds);
            })
            ->count();
        
        // Get approved status cuti (S02 = Disetujui)
        $approvedStatus = StatusCuti::where('kode', 'S02')->first();
        
        // Count users with approved izin sakit
        $izinSakitCount = IzinSakit::where('tanggal', $date)
            ->where('deleted_at', null)
            ->whereHas('user', function($query) use ($roleIds) {
                $query->whereIn('role_id', $roleIds);
            })
            ->count();
        
        // Count users with approved cuti
        $cutiCount = 0;
        if ($approvedStatus) {
            $cutiCount = Cuti::where('status_cuti_id', $approvedStatus->id)
                ->where('deleted_at', null)
                ->where('tanggal_mulai', '<=', $date)
                ->where('tanggal_akhir', '>=', $date)
                ->whereHas('user', function($query) use ($roleIds) {
                    $query->whereIn('role_id', $roleIds);
                })
                ->count();
        }
        
        $onLeave = $izinSakitCount + $cutiCount;
        $absent = $totalEmployees - $checkedIn - $onLeave;
        $attendanceRate = $totalEmployees > 0 ? round(($checkedIn / $totalEmployees) * 100, 2) : 0;
        
        return [
            'total_employees' => $totalEmployees,
            'checked_in' => $checkedIn,
            'absent' => $absent,
            'on_leave' => $onLeave,
            'attendance_rate' => $attendanceRate,
            'is_weekend' => false
        ];
    }
    
    /**
     * Create attendance records with status "Alpha / Tidak Hadir" for users without approved leave
     *
     * @param string|null $date Date in Y-m-d format, defaults to today
     * @return int Number of records created
     */
    public function createAbsentRecords(?string $date = null): int
    {
        $date = $date ?? Carbon::now()->format('Y-m-d');
        
        // Check if it's weekend (Saturday = 6, Sunday = 0)
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        if ($dayOfWeek == 0 || $dayOfWeek == 6) {
            return 0; // No records created on weekends
        }
        
        // Get absent employees
        $absentUsers = $this->getAbsentEmployees($date);
        
        $recordsCreated = 0;
        
        foreach ($absentUsers as $user) {
            // Check if attendance record already exists for this user and date
            $existingRecord = Absensi::where('user_id', $user->id)
                ->where('deleted_at', null)
                ->where('tanggal', $date)
                ->first();
            
            if (!$existingRecord) {
                // Get user information
                $userInfo = $user->userInformation;
                $userName = $userInfo ? $userInfo->nama : 'Unknown';
                $category = KategoriAbsensi::where('code', 'A1')->first();
                
                // Create attendance record with status "Alpha / Tidak Hadir"
                Absensi::create([
                    'uuid' => \App\Helpers\Generate::uuid(),
                    'user_id' => $user->id,
                    'nama_karyawan' => $userName,
                    'tanggal' => $date,
                    'keterangan' => 'Tidak hadir tanpa izin',
                    'status_absen' => 'Alpha / Tidak Hadir',
                    'kategori_absensi_id' => $category->id,
                    'nama_kategori' => $category->name,
                    'jumlah_point' => 0,
                    'is_active' => 1,
                    'created_at' => time()
                ]);
                
                $recordsCreated++;
            }
        }
        
        return $recordsCreated;
    }
}