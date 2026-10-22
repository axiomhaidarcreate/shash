<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Employee;

$users = User::all();
$count = 0;

foreach ($users as $user) {
    if (!Employee::where('user_id', $user->id)->orWhere('email', $user->email)->exists()) {
        Employee::create([
            'user_id' => $user->id,
            'full_name' => $user->name,
            'full_name_ar' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
            'employee_code' => 'EMP-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
            'hire_date' => $user->created_at ?? now(),
            'is_active' => true,
            'working_days' => 6,
            'daily_work_hours' => 8,
            'base_salary' => 0,
            'phone' => '0500000000',
            'national_id' => '1000000000',
            'gender' => 'male',
        ]);
        $count++;
    } else {
        // If employee exists by email but has no user_id, update it
        $emp = Employee::where('email', $user->email)->first();
        if ($emp && !$emp->user_id) {
            $emp->user_id = $user->id;
            $emp->save();
        }
    }
}
echo "Copied $count employees.\n";
