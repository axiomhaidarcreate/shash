<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Str;

class ImportOldEmployees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hr:import-old-employees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import old users into the new HR employees table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            $existing = Employee::where('email', $user->email)->first();
            
            if (!$existing) {
                // Generate a unique employee code
                $employeeCode = 'EMP-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);
                
                Employee::create([
                    'full_name' => $user->name,
                    'full_name_ar' => $user->name,
                    'email' => $user->email,
                    'password' => $user->password,
                    'employee_code' => $employeeCode,
                    'phone' => '0000000000', // Required field
                    'is_active' => true,
                ]);
                
                $count++;
                $this->line("Imported: {$user->name}");
            }
        }

        $this->info("Successfully imported {$count} old employees into the HR module.");
    }
}
