<?php

use App\Http\Controllers\Api\MarkdownAttachmentController;
use App\Http\Controllers\Api\NewsletterSubscriberController;
use App\Http\Controllers\PaymobController;
use App\Jobs\TestQueueJob;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

Route::get('/', function () {
    abort(404);
});

Route::get('/password/reset/{token}', function (Request $request, $token) {
    $email = $request->query('email');

    $frontend = rtrim(config('app.frontend_url'), '/');

    if (!$email) {
        return redirect($frontend.'/?reset=invalid');
    }

    $table = config('auth.passwords.users.table', 'password_reset_tokens');
    $record = DB::table($table)->where('email', $email)->first();

    if (!$record || !Hash::check($token, $record->token)) {
        // توكن غلط
        return redirect($frontend.'/?reset=invalid');
    }

    $expires = (int) config('auth.passwords.users.expire', 60); // بالدقايق
    $expired = Carbon::parse($record->created_at)
        ->addMinutes($expires)
        ->isPast();

    if ($expired) {
        return redirect($frontend.'/?reset=expired');
    }

    return redirect($frontend."/reset-password/{$token}?email={$email}");
})->name('password.reset.link');


Route::get('/newsletter/verify/{id}/{hash}', [NewsletterSubscriberController::class, 'verify'])
    ->name('newsletter.verify')
    ->middleware('signed');


Route::get('/test-queue', function () {
    dispatch(new TestQueueJob());
    return 'Job dispatched!';
});

Route::post('/admin/blogs/attachments', [MarkdownAttachmentController::class, 'store'])
    ->middleware(['web', 'auth'])
    ->name('filament.admin.blogs.upload-attachment');

    
Route::get('/payment/paymob/callback', [PaymobController::class, 'handleRedirect'])->name('paymob.callback');

Route::get('test-push', fn()=> 'so so bad');

Route::get('/test-paymob', [App\Http\Controllers\PaymobController::class, 'testPayment']);

Route::get('/final-test-2', function() {
    return 'All tests done222222222222222222222222222222222';
});

Route::get('/check-rim/{id}', function($id) {
    $rim = \App\Models\Rim::find($id);
    if (!$rim) return "Rim not found";
    
    return [
        'name' => $rim->name,
        'has_media' => $rim->hasMedia('rim_feature_image'),
        'media_count' => $rim->getMedia('rim_feature_image')->count(),
        'rim_feature_image_url' => $rim->rim_feature_image_url,
        'feature_image_url' => $rim->feature_image_url,
        'original_url' => $rim->original_url,
        'media_details' => $rim->getMedia('rim_feature_image')->map(function($media) {
            return [
                'id' => $media->id,
                'file_name' => $media->file_name,
                'url' => $media->getUrl(),
                'path' => $media->getPath(),
                'exists' => file_exists($media->getPath())
            ];
        })
    ];
});

// Email Preview Routes
Route::prefix('test-email')->group(function () {
    
    // Password Reset Email
    Route::get('/password-reset', function () {
        $token = 'sample-token-123456';
        $email = 'test@example.com';
        
        return view('emails.password-reset', compact('token', 'email'));
    });
    
    // Verify Email
    Route::get('/verify-email', function () {
        $verificationUrl = config('app.frontend_url') . '/verify-email?token=sample-token';
        
        return view('emails.verify-email', compact('verificationUrl'));
    });
    
    // Coupon Email
    Route::get('/coupon', function () {
        $coupon = (object)[
            'code' => 'SAVE20',
            'type' => 'discount_percentage',
            'value' => 20,
            'min_order_amount' => 100,
            'expires_at' => now()->addDays(30)
        ];
        
        return view('emails.coupon', compact('coupon'));
    });
    
    // Inquiry Confirmation Email
    Route::get('/inquiry-confirmation', function () {
        $inquiry = (object)[
            'id' => 12345,
            'full_name' => 'John Doe'
        ];
        
        return view('emails.inquiry-confirmation', compact('inquiry'));
    });
    
    // Order Receipt Email
    Route::get('/receipt', function () {
        $order = \App\Models\Order::with(['items', 'shippingAddress.area', 'shippingAddress.city', 'user'])->latest()->first();
        
        if (!$order) {
            // Create sample order data if no orders exist
            $order = (object)[
                'id' => 'SAMPLE-001',
                'created_at' => now(),
                'user' => (object)['name' => 'John Doe'],
                'contact_name' => 'John Doe',
                'payment_method' => 'cash_on_delivery',
                'tracking_number' => 'TRACK123456',
                'subtotal' => 500.00,
                'shipping_cost' => 35.00,
                'tax_amount' => 26.75,
                'total' => 561.75,
                'items' => [
                    (object)[
                        'product_name' => 'Michelin Pilot Sport 4',
                        'quantity' => 4,
                        'price_per_unit' => 125.00,
                        'subtotal' => 500.00
                    ]
                ],
                'shippingAddress' => (object)[
                    'address_line' => '123 Test Street, Building 5, Apt 12',
                    'phone' => '+971501234567',
                    'area' => (object)['name' => 'Dubai Marina'],
                    'city' => (object)['name' => 'Dubai']
                ]
            ];
        }
        
        return view('emails.orders.receipt', compact('order'));
    });
    
    // Jeebly Low Wallet Alert
    Route::get('/jeebly-low-wallet', function () {
        $errorData = [
            'status' => 400,
            'message' => 'Wallet balance is low',
            'order_id' => 12345,
            'response_body' => '{"success":"false","message":"Wallet balance is low"}'
        ];
        
        return view('emails.jeebly-low-wallet', compact('errorData'));
    });
    
    // List all available email templates
    Route::get('/', function () {
        return view('emails.test-index');
    });
});

// Great , now I want to implement same progress bar at the all importers