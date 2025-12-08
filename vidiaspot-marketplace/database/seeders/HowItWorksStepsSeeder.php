<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HowItWorksStep;

class HowItWorksStepsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default how-it-works steps
        $steps = [
            [
                'title' => 'Create Account',
                'description' => 'Sign up for a free account in less than a minute to start buying or selling.',
                'icon_class' => 'fas fa-user-plus',
                'step_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Post & Search',
                'description' => 'Post your items for sale or browse through thousands of listings in your area.',
                'icon_class' => 'fas fa-plus',
                'step_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Connect & Complete',
                'description' => 'Connect with buyers or sellers and complete your transaction safely and easily.',
                'icon_class' => 'fas fa-handshake',
                'step_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($steps as $step) {
            HowItWorksStep::create($step);
        }
    }
}
