<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Livewire\DatabaseNotifications;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Comment;
use App\Observers\CommentObserver;
use App\Observers\TicketObserver;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    protected $observers = [
        //
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // DB::statement("SET time_zone = '+08:00'");
        DatabaseNotifications::pollingInterval('20s');
        Comment::observe(CommentObserver::class);
        Ticket::observe(TicketObserver::class);
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Welcome! Please Verify Your Email Address') // Your custom subject line
                ->greeting('Hello ' . $notifiable->name . '!') // Example: Greet the user by name
                ->line('Thank you for registering with ' . config('app.name') . '!') // Custom introductory line
                ->line('Before you can fully access your account, please click the button below to verify your email address.') // Main instruction
                ->action('Verify My Email Address', $url) // The verification button
                ->line('If you did not create an account, no further action is required.'); // Closing line
        });

        // Add this condition
        if ($this->app->environment('local')) {
            URL::forceScheme('https');
        }

        // ResetPassword::toMailUsing(function (object $notifiable, string $token) {
        //     // Construct the password reset URL
        //     // Ensure this URL matches your actual password reset route (e.g., /reset-password)
        //     $url = url(route('filament.ticketing.auth.password-reset.reset', [
        //         'token' => $token,
        //         'email' => $notifiable->getEmailForPasswordReset(),
        //     ])); // Use false for relative URL if needed, but absolute is safer for emails

        //     return (new MailMessage)
        //         ->subject('Reset Your Password') // Custom subject line
        //         ->greeting('Hello ' . $notifiable->name . '!') // <-- Customize the greeting here
        //         ->line('You are receiving this email because we received a password reset request for your account.')
        //         ->action('Reset Password', $url) // The reset password button
        //         ->line('This password reset link will expire in ' . config('auth.passwords.' . config('auth.defaults.passwords') . '.expire') . ' minutes.') // Dynamic expiration
        //         ->line('If you did not request a password reset, no further action is required.');
        // });
    }
}
