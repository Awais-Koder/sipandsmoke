<?php

namespace Database\Seeders;

use App\Models\GalleryPhoto;
use Illuminate\Database\Seeder;

class GalleryPhotoSeeder extends Seeder
{
    public function run(): void
    {
        if (GalleryPhoto::query()->exists()) {
            return;
        }

        $rows = json_decode(
            (string) file_get_contents(database_path('seeders/data/gallery-photos.json')),
            true
        );

        foreach (array_chunk($rows, 200) as $chunk) {
            GalleryPhoto::query()->insert(array_map(function (array $row) {
                unset($row['width'], $row['height']);

                return $row + [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $chunk));
        }
    }
}
