<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = [
            ['nama_vendor' => 'Warung Bu Siti', 'kode_vendor' => 'WBS01'],
            ['nama_vendor' => 'Ayam Geprek Juara', 'kode_vendor' => 'AGJ02'],
            ['nama_vendor' => 'Nasi Uduk Mande', 'kode_vendor' => 'NUM03'],
            ['nama_vendor' => 'Kedai Kopi Senja', 'kode_vendor' => 'KKS04'],
        ];

        foreach ($vendors as $vendor) {
            Vendor::create($vendor);
        }
    }
}
