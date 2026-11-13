<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Support\SiteContent;
use Illuminate\Database\Seeder;

class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        foreach (SiteContent::defaults() as $key => $value) {
            Setting::query()->firstOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        Setting::forgetCache();
    }
}
