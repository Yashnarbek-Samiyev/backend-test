<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $koylak = Product::create(['name' => 'Koylak', 'code' => 'K001']);
        $shim = Product::create(['name' => 'Shim', 'code' => 'S001']);

        $mato = Material::create(['name' => 'Mato']);
        $ip = Material::create(['name' => 'Ip']);
        $tugma = Material::create(['name' => 'Tugma']);
        $zamok = Material::create(['name' => 'Zamok']);

        $koylak->materials()->attach([
            $mato->id => ['quantity' => 0.8],
            $tugma->id => ['quantity' => 5],
            $ip->id => ['quantity' => 10],
        ]);

        $shim->materials()->attach([
            $mato->id => ['quantity' => 1.4],
            $ip->id => ['quantity' => 15],
            $zamok->id => ['quantity' => 1],
        ]);

        Warehouse::create(['material_id' => $mato->id, 'remainder' => 12, 'price' => 1500]);
        Warehouse::create(['material_id' => $mato->id, 'remainder' => 200, 'price' => 1600]);
        Warehouse::create(['material_id' => $ip->id, 'remainder' => 40, 'price' => 500]);
        Warehouse::create(['material_id' => $ip->id, 'remainder' => 300, 'price' => 550]);
        Warehouse::create(['material_id' => $tugma->id, 'remainder' => 500, 'price' => 300]);
        Warehouse::create(['material_id' => $zamok->id, 'remainder' => 1000, 'price' => 2000]);

//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'test@example.com',
//        ]);
    }
}
