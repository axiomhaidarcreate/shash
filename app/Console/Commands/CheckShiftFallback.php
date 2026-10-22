<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use App\Models\WorkShift;
use App\Services\ShiftService;
use Carbon\Carbon;

class CheckShiftFallback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shift:check-fallback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks and performs auto-open/auto-close for work shifts at specified times.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTimeStr = Setting::where('key', 'shift_start_time')->value('value');
        $endTimeStr = Setting::where('key', 'shift_end_time')->value('value');

        if (!$startTimeStr || !$endTimeStr) {
            $this->info('Shift times are not configured. Exiting.');
            return;
        }

        $now = Carbon::now();
        $businessDate = $now->copy()->subHours(8)->toDateString(); // Shifts starting at 16:00 belong to today until 08:00 tomorrow

        // Parse today's start and end times
        $shiftStart = Carbon::parse($businessDate . ' ' . $startTimeStr);
        $shiftEnd = Carbon::parse($businessDate . ' ' . $endTimeStr);
        
        // Handle cases where shift ends on the next day (e.g. start 16:00, end 02:00)
        if ($shiftEnd->lessThan($shiftStart)) {
            $shiftEnd->addDay();
        }

        $activeShift = WorkShift::where('status', 'open')->first();
        $shiftService = new ShiftService();

        // 1. Auto-Open Fallback
        if (!$activeShift) {
            $lastShiftBusinessDate = Setting::where('key', 'last_shift_business_date')->value('value');
            
            // Only auto-open if we haven't already opened a shift for this business day
            if ($lastShiftBusinessDate !== $businessDate) {
                $autoStartTime = $shiftStart->copy();
                
                if ($now->greaterThanOrEqualTo($autoStartTime) && $now->lessThan($shiftEnd)) {
                    $this->info("Fallback: Auto-starting shift at {$now}");
                    $shiftService->startShift(null, true);
                }
            }
        }

        // 2. Auto-Close Fallback
        if ($activeShift) {
            $autoCloseTime = $shiftEnd->copy();
            
            if ($now->greaterThanOrEqualTo($autoCloseTime)) {
                $this->info("Fallback: Auto-closing shift at {$now}");
                $shiftService->closeShift(null, true);
            }
        }

        $this->info('Check completed.');
    }
}
