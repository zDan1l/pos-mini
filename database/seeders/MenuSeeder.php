<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = Vendor::all();

        foreach ($vendors as $vendor) {
            $menus = match($vendor->kode_vendor) {
                'WBS01' => [
                    ['nama_menu' => 'Nasi Rawon', 'harga' => 18000],
                    ['nama_menu' => 'Nasi Pecel', 'harga' => 12000],
                    ['nama_menu' => 'Tahu Tek', 'harga' => 10000],
                    ['nama_menu' => 'Es Teh Manis', 'harga' => 3000],
                ],
                'AGJ02' => [
                    ['nama_menu' => 'Ayam Geprek Original', 'harga' => 12000],
                    ['nama_menu' => 'Ayam Geprek Keju', 'harga' => 15000],
                    ['nama_menu' => 'Ayam Geprek Mozarella', 'harga' => 18000],
                    ['nama_menu' => 'Es Jeruk', 'harga' => 5000],
                ],
                'NUM03' => [
                    ['nama_menu' => 'Nasi Uduk Komplit', 'harga' => 20000],
                    ['nama_menu' => 'Nasi Uduk Telur', 'harga' => 15000],
                    ['nama_menu' => 'Nasi Uduk Teri', 'harga' => 12000],
                    ['nama_menu' => 'Kopi Hitam', 'harga' => 5000],
                ],
                'KKS04' => [
                    ['nama_menu' => 'Americano', 'harga' => 18000],
                    ['nama_menu' => 'Cappuccino', 'harga' => 20000],
                    ['nama_menu' => 'Mocha Latte', 'harga' => 22000],
                    ['nama_menu' => 'Roti Bakar Coklat', 'harga' => 12000],
                    ['nama_menu' => 'Roti Bakar Keju', 'harga' => 15000],
                ],
                default => []
            };

            foreach ($menus as $menu) {
                Menu::create([
                    'nama_menu' => $menu['nama_menu'],
                    'harga' => $menu['harga'],
                    'idvendor' => $vendor->idvendor,
                    'is_available' => true,
                ]);
            }
        }
    }
}
