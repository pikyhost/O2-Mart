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

// notes: buy_3_get_1 => tyres only


// 4 tyres => pricing of 3 ,4 tyres
// 5 tyres => pricing of 4 ,5 tyres




// chatbot systme
/*

1 - backend_name for the chat bot 
2 - instead of desc , replace name with "System prompt"



talbe:
1 -at supervisor select the teacher
2 - إحصائيات الحصص
3 - print pdf , the name of scheool 
4- save pdf

*/



Route::get('/final-test-2', function() {
    return 'All tests done222222222222222222222222222222222';
});

Route::get('/fix-rim-images/{id?}', function($id = null) {
    if (app()->environment('production')) {
        abort(404);
    }
    
    $fixed = \App\Models\Rim::fixImageIssues($id);
    $message = $id ? "Fixed image for rim ID {$id}" : "Fixed images for {$fixed} rims";
    return $message;
});