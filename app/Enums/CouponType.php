<?php

namespace App\Enums;

enum CouponType: string
{
    case FREE_SHIPPING = 'free_shipping';
    case DISCOUNT_PERCENTAGE = 'discount_percentage';
    case DISCOUNT_AMOUNT = 'discount_amount';
}
