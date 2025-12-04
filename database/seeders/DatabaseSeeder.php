<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Setting::query()->create([
            'key' => 'competetion_start',
            'label' => 'بدء المسابقة',
            'value' => 0
        ]);
    }
}
