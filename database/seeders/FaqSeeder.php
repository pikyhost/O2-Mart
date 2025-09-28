<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        Faq::create([
            'items' => [
                // General Questions
                [
                    'category' => 'general',
                    'question' => 'What is your return policy?',
                    'answer' => 'We do not recommend the general internal use of essential oils. Essential oils are highly concentrated and have the capacity to cause serious damage if used internally without the necessary expertise.',
                ],
                [
                    'category' => 'general',
                    'question' => 'Do you ship internationally?',
                    'answer' => 'Yes, we offer international shipping for most destinations.',
                ],
                [
                    'category' => 'general',
                    'question' => 'Can I change my order after placing it?',
                    'answer' => 'Unfortunately, once an order is placed, it cannot be changed or cancelled.',
                ],
                [
                    'category' => 'general',
                    'question' => 'How can I track my shipment?',
                    'answer' => 'Once your order is shipped, you will receive an email with tracking details.',
                ],

                // Payment & Gift Card
                [
                    'category' => 'payment',
                    'question' => 'What payment methods do you accept?',
                    'answer' => 'We accept Visa, Mastercard, PayPal, and store gift cards.',
                ],
                [
                    'category' => 'payment',
                    'question' => 'How do I use a gift card?',
                    'answer' => 'You can enter your gift card code during checkout to apply it to your order.',
                ],
                [
                    'category' => 'payment',
                    'question' => 'Is it safe to use my credit card online?',
                    'answer' => 'Yes. We use secure SSL encryption to protect your data.',
                ],
                [
                    'category' => 'payment',
                    'question' => 'Can I get a refund to my gift card?',
                    'answer' => 'If your order is eligible for a refund, it will be returned to your original payment method, including gift cards.',
                ],
            ]
        ]);
    }
}
