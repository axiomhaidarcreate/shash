<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesAndUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['admin', 'order_staff', 'kitchen_staff', 'delivery_staff', 'inventory_staff'];
        foreach ($roles as $role) {
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role]);
        }

        $admin = \App\Models\User::firstOrCreate([
            'email' => 'admin@shisha.com',
        ], [
            'name' => 'Admin Manager',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        $orderStaff = \App\Models\User::firstOrCreate([
            'email' => 'order@shisha.com',
        ], [
            'name' => 'Order Staff 1',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);
        $orderStaff->assignRole('order_staff');

        $kitchenStaff = \App\Models\User::firstOrCreate([
            'email' => 'kitchen@shisha.com',
        ], [
            'name' => 'Kitchen Staff 1',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);
        $kitchenStaff->assignRole('kitchen_staff');

        $deliveryStaff = \App\Models\User::firstOrCreate([
            'email' => 'delivery@shisha.com',
        ], [
            'name' => 'Delivery Staff 1',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);
        $deliveryStaff->assignRole('delivery_staff');
        $inventoryStaff = \App\Models\User::firstOrCreate([
            'email' => 'inventory@shisha.com',
        ], [
            'name' => 'Inventory Staff 1',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);
        $inventoryStaff->assignRole('inventory_staff');
    }
}
