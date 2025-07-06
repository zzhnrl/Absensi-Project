<?php

namespace App\Console\Commands;

use App\Services\AttendanceCheckService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckAbsentEmployees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:check-absent-employees {--date= : Check for specific date (Y-m-d format)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check which employees with karyawan role have not checked in by 5 PM and set status to tidak masuk';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = $this->option('date');
        $service = new AttendanceCheckService();
        
        // Get absent employees (no attendance and no approved leave)
        $absentUsers = $service->getAbsentEmployees($date);
        
        // Create attendance records with status "Alpha / Tidak Hadir"
        $recordsCreated = $service->createAbsentRecords($date);
        
        // Get attendance statistics
        $stats = $service->getAttendanceStats($date);
        
        // Log the results silently
        Log::info("Attendance check completed at " . now()->format('Y-m-d H:i:s'), [
            'date' => $date ?? now()->format('Y-m-d'),
            'total_employees' => $stats['total_employees'],
            'checked_in' => $stats['checked_in'],
            'on_leave' => $stats['on_leave'],
            'tidak_masuk' => $stats['absent'],
            'attendance_rate' => $stats['attendance_rate'],
            'absent_employees' => $absentUsers->pluck('email')->toArray(),
            'alpha_records_created' => $recordsCreated
        ]);
        
        return 0;
    }
} 