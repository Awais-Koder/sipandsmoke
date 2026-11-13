<?php

namespace Database\Seeders;

use App\Models\MenuPage;
use Illuminate\Database\Seeder;

class MenuPageSeeder extends Seeder
{
    public function run(): void
    {
        if (MenuPage::query()->exists()) {
            return;
        }

        $rows = json_decode(
            (string) file_get_contents(database_path('seeders/data/menu-pages.json')),
            true
        );

        foreach ($rows as $row) {
            MenuPage::query()->create($row + ['is_active' => true]);
        }
    }
}
