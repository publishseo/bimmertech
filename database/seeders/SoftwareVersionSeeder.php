<?php

namespace Database\Seeders;

use App\Models\SoftwareVersion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SoftwareVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(database_path('seeders/softwareversions.json'));
        $data = json_decode($json, true);

        foreach ($data as $item) {
            SoftwareVersion::create($item);
        }
    }
}
