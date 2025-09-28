<?php

return [

    'tiers' => [
        // [max shipments per month, normal cost, remote cost]
        ['max' => 250, 'normal' => 14, 'remote' => 49],
        ['max' => 500, 'normal' => 12, 'remote' => 47],
        ['max' => PHP_INT_MAX, 'normal' => 11, 'remote' => 46],
    ],

    'extra_per_kg' => 2,

    'fuel_percent' => 0.02,

    'epg_percent' => 0.10,
    'epg_min' => 2,

    'packaging_fee' => 5.25,

    'vat_percent' => 0.05,

    'max_weight' => 20,

    'volumetric_divisor' => 5000,

    'remote_areas' => [
        'Ghayathi', 'Bad Al Matawa', 'Al Nadra', 'Al Hmara', 'BARAKA', 'AL SILA',
        'Madinat Zayed', 'Habshan', 'Bainuona', 'LIWA', 'ASAB', 'HAMEEM', 'Ruwais',
        'Mirfa', 'Abu Al Bayad', 'AL Hamra', 'Jabel Al Dhani', 'Hatta', 'Nazwa',
        'Lehbab', 'Madam', 'Masfout', 'Wadi Al Shiji', 'Al showka', 'Nahil', 'Sawehan',
        'Al Dahra', 'Al Qua', 'Al Wagan',
    ],
];
