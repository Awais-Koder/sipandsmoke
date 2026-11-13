<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        if (Product::query()->exists()) {
            return;
        }

        $rows = json_decode(
            (string) file_get_contents(database_path('seeders/data/products.json')),
            true
        );

        foreach ($rows as $row) {
            unset($row['width'], $row['height']);
            Product::query()->create($row + ['is_active' => true]);
        }
    }
}
