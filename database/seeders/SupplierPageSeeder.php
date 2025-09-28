<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SupplierPage;

class SupplierPageSeeder extends Seeder
{
    public function run(): void
    {
        SupplierPage::create([
            'title_become_supplier' => 'Become a Supplier',
            'desc_become_supplier' => '## Why Partner With Us?

We offer **great tools**, fast onboarding, and trusted support to help you grow.',
            'why_auto_title' => 'Why Auto?',
            'why_auto_desc' => '- Automated logistics
- Smart inventory
- 24/7 analytics dashboard',
        ]);
    }
}
