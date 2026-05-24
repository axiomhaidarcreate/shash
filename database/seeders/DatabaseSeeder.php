<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndUsersSeeder::class);

        // Create 10 tables
        for ($i = 1; $i <= 10; $i++) {
            \App\Models\Table::firstOrCreate(['number' => 'T' . $i]);
        }

        // Default products
        $shishaFlavors = [
            'تفاحتين', 'عنب نعناع', 'ليمون نعناع', 'بطيخ نعناع',
            'بلو ميكس', 'نعناع سادة', 'توت مشكل', 'خوخ نعناع',
            'علكة', 'بطيخ توت', 'موهيتو', 'كاكتوس',
        ];
        foreach ($shishaFlavors as $flavor) {
            \App\Models\Product::firstOrCreate(['name' => $flavor], ['category' => 'معسل']);
        }
    }
}
