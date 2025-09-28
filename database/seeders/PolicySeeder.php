<?php

namespace Database\Seeders;

use App\Models\Policy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert default policy
        Policy::updateOrCreate(
            ['id' => 1], // Only one record
            [
                'privacy_policy'   => '# Privacy Policy',
                'refund_policy'    => '# Refund Policy',
                'terms_of_service' => '# Terms of Service',
            ]
        );
    }
}
