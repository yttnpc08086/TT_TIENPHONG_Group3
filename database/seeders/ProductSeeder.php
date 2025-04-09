<?php

// database/seeders/ProductSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->insert([
            [
                'name' => 'Smartphone',
                'description' => 'A high-end smartphone.',
                'price' => 699.99,
                'stock' => 50,
                'category_id' => 2, // Electronics category
            ],
            [
                'name' => 'T-shirt',
                'description' => 'Comfortable cotton t-shirt.',
                'price' => 19.99,
                'stock' => 100,
                'category_id' => 3, // Clothing category
            ],
            [
                'name' => 'Programming Book',
                'description' => 'Learn to code with this book.',
                'price' => 29.99,
                'stock' => 30,
                'category_id' => 4, // Books category
            ],
        ]);
    }
}
