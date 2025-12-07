<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'flag_icon' => '🇬🇧',
                'is_default' => true,
                'is_rtl' => false,
                'dialects' => json_encode(['Yoruba', 'Igbo', 'Hausa']),
                'is_active' => true,
            ],
            [
                'code' => 'fr',
                'name' => 'French',
                'native_name' => 'Français',
                'flag_icon' => '🇫🇷',
                'is_rtl' => false,
                'is_active' => true,
            ],
            [
                'code' => 'pt',
                'name' => 'Portuguese',
                'native_name' => 'Português',
                'flag_icon' => '🇵🇹',
                'is_rtl' => false,
                'is_active' => true,
            ],
            [
                'code' => 'ar',
                'name' => 'Arabic',
                'native_name' => 'العربية',
                'flag_icon' => '🇸🇦',
                'is_rtl' => true,
                'is_active' => true,
            ],
            [
                'code' => 'es',
                'name' => 'Spanish',
                'native_name' => 'Español',
                'flag_icon' => '🇪🇸',
                'is_rtl' => false,
                'is_active' => true,
            ],
            [
                'code' => 'de',
                'name' => 'German',
                'native_name' => 'Deutsch',
                'flag_icon' => '🇩🇪',
                'is_rtl' => false,
                'is_active' => true,
            ],
            [
                'code' => 'zh',
                'name' => 'Chinese',
                'native_name' => '中文',
                'flag_icon' => '🇨🇳',
                'is_rtl' => false,
                'is_active' => true,
            ],
            [
                'code' => 'yo',
                'name' => 'Yoruba',
                'native_name' => 'Yorùbá',
                'flag_icon' => '🇳🇬',
                'is_rtl' => false,
                'is_active' => true,
            ],
            [
                'code' => 'ig',
                'name' => 'Igbo',
                'native_name' => 'Igbo',
                'flag_icon' => '🇳🇬',
                'is_rtl' => false,
                'is_active' => true,
            ],
            [
                'code' => 'ha',
                'name' => 'Hausa',
                'native_name' => 'Hausa',
                'flag_icon' => '🇳🇬',
                'is_rtl' => false,
                'is_active' => true,
            ],
        ];

        foreach ($languages as $language) {
            DB::table('languages')->updateOrInsert(
                ['code' => $language['code']],
                $language
            );
        }
    }
}
