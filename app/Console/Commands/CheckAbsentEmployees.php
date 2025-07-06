<?php

namespace App\Console\Commands;

use App\Services\AttendanceCheckService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
    protected $description = 'Check which employees with karyawan and manajer roles have not checked in by 5 PM and set status to tidak masuk';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = $this->option('date');
        $service = new AttendanceCheckService();
        
        // Check if it's weekend
        $checkDate = $date ?? now()->format('Y-m-d');
        $dayOfWeek = Carbon::parse($checkDate)->dayOfWeek;
        $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);
        
        if ($isWeekend) {
            // Log weekend message
            Log::info("Attendance check skipped - Weekend detected at " . now()->format('Y-m-d H:i:s'), [
                'date' => $checkDate,
                'day_of_week' => $dayOfWeek,
                'is_weekend' => true,
                'message' => 'Attendance check skipped on weekends'
            ]);
            return 0;
        }
        
        // Get absent employees (no attendance and no approved leave)
        $absentUsers = $service->getAbsentEmployees($date);
        
        // Create attendance records with status "Alpha / Tidak Hadir"
        $recordsCreated = $service->createAbsentRecords($date);
        
        // Get attendance statistics
        $stats = $service->getAttendanceStats($date);
        
        // Log the results silently
        Log::info("Attendance check completed at " . now()->format('Y-m-d H:i:s'), [
            'date' => $date ?? now()->format('Y-m-d'),
            'day_of_week' => $dayOfWeek,
            'is_weekend' => false,
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