<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;

class NewsletterSubscriberController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid input',
                'details' => $validator->errors(),
            ], 422);
        }

        $email = strtolower(trim($request->input('email')));

        $existing = NewsletterSubscriber::where('email', $email)->first();

        if ($existing) {
            if (is_null($existing->verified_at)) {
                if (!Cache::has("newsletter_verify_sent:{$existing->id}")) {
                    Notification::send($existing, new \App\Notifications\VerifyNewsletterSubscription($existing));
                    Cache::put("newsletter_verify_sent:{$existing->id}", true, now()->addMinutes(5));
                }

                Notification::send($existing, new \App\Notifications\VerifyNewsletterSubscription($existing));

                return response()->json([
                    'message' => 'You are already subscribed but not verified yet. We have re-sent the verification email.',
                    'status'  => 'already_subscribed_unverified',
                ], 200);
            }

            
            return response()->json([
                'message' => 'You are already subscribed to the newsletter.',
                'status'  => 'already_subscribed',
            ], 200);
        }

        try {
            $subscriber = NewsletterSubscriber::create([
                'email' => $email,
                'ip_address' => $request->ip(),
            ]);

            // Send verification email
            Notification::send($subscriber, new \App\Notifications\VerifyNewsletterSubscription($subscriber));

            $adminUsers = User::role(['admin', 'super_admin'])->get();

            foreach ($adminUsers as $admin) {
                App::setLocale($admin->preferred_language ?? config('app.locale'));

                \Filament\Notifications\Notification::make()
                    ->title(__('New Newsletter Subscription'))
                    ->success()
                    ->body(__('A new user has subscribed to the newsletter with the email: :email', [
                        'email' => $subscriber->email,
                    ]))
                    ->actions([
                        Action::make('view')
                            ->label(__('View Subscribers'))
                            ->url(route('filament.admin.resources.newsletter-subscribers.index'))
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($admin);
            }

            return response()->json([
                'message' => 'Subscription request received. Please check your email to verify.',
                'status'  => 'created',
            ], 201);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle duplicate email (race condition)
            if ($e->getCode() === '23000') {
                return response()->json([
                    'message' => 'You are already subscribed to the newsletter.',
                    'status'  => 'already_subscribed',
                ], 200);
            }

            // Handle other database errors
            return response()->json([
                'error' => 'Failed to process subscription. Please try again later.',
            ], 500);
        }
    }

    public function verify(Request $request, int $id, string $hash)
    {
        $frontendUrl = config('app.frontend_url');

        $subscriber = NewsletterSubscriber::find($id);

        if (!$subscriber) {
            return redirect($frontendUrl . '?newsletter=error');
        }

        if (!hash_equals($hash, sha1($subscriber->email))) {
            return redirect($frontendUrl . '?newsletter=error');
        }

        if (!$request->hasValidSignature()) {
            return redirect($frontendUrl . '?newsletter=error');
        }

        if ($subscriber->verified_at) {
            return redirect($frontendUrl . '?newsletter=already');
        }

        $subscriber->update(['verified_at' => now()]);

        \Filament\Notifications\Notification::make()
            ->title(__('Newsletter Subscription Confirmed'))
            ->success()
            ->body(__('Your email (:email) has been successfully verified for the newsletter.', [
                'email' => $subscriber->email,
            ]))
            ->sendToDatabase(User::role(['admin', 'super_admin'])->get());

        return redirect($frontendUrl . '?newsletter=verified');
    }


}
