<?php

namespace App\Filament\Pages\Auth;

use DiogoGPinto\AuthUIEnhancer\Pages\Auth\Concerns\HasCustomLayout;
use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Validation\ValidationException;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

class Login extends BaseLogin
{
    use HasCustomLayout;

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        } elseif (
            $user->is_active === 0
        ) {
            Filament::auth()->logout();

            throw ValidationException::withMessages([
                'data.email' => 'Your account is inactive.',
            ]);
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }
}
