<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new roles
        Role::firstOrCreate(['name' => 'barista_staff']);
        Role::firstOrCreate(['name' => 'restaurant_staff']);

        // Create a default user for Barista
        $barista = User::firstOrCreate([
            'email' => 'barista@shisha.com',
        ], [
            'name' => 'Barista Staff 1',
            'password' => Hash::make('password'),
        ]);
        if (!$barista->hasRole('barista_staff')) {
            $barista->assignRole('barista_staff');
        }

        // Create a default user for Restaurant
        $restaurant = User::firstOrCreate([
            'email' => 'restaurant@shisha.com',
        ], [
            'name' => 'Restaurant Staff 1',
            'password' => Hash::make('password'),
        ]);
        if (!$restaurant->hasRole('restaurant_staff')) {
            $restaurant->assignRole('restaurant_staff');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optional down migration, usually we don't delete roles in down to prevent breaking constraints
    }
};
