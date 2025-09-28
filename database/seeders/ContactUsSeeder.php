<?php

namespace Database\Seeders;

use App\Models\ContactUs;
use Illuminate\Database\Seeder;

class ContactUsSeeder extends Seeder
{
    public function run(): void
    {
        ContactUs::create([
            'image' => 'contact-us/contact-hero.jpg',
            'heading' => 'Get in Touch With Us',
            'description' => '<p>We\'d love to hear from you! Whether you have a question about our services, need assistance, or just want to say hello, our team is ready to help.</p>',

            'title' => 'Our Contact Information',
            'title_desc' => '<p>Feel free to reach out through any of the following channels. We typically respond within 24 hours.</p>',

            'form_title' => 'Send Us a Message',
            'form_desc' => '<p>Fill out the form below and we\'ll get back to you as soon as possible.</p>',

            'address' => '123 Business Street, Suite 100, San Francisco, CA 94107',
            'email' => 'info@yourcompany.com',
            'whatsapp' => '+1 (555) 123-4567',
        ]);
    }
}
