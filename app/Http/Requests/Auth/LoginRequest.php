<?php

namespace App\Http\Requests\Auth;

use App\Models\Lecturer;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Field keeps the name `email` but accepts either an email or a
            // Lecturer ID (code) — see resolveEmail().
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * A lecturer can sign in with their Lecturer ID instead of an email.
     * Anything without an "@" is looked up as a lecturer code and mapped to
     * that lecturer's linked user's email; an unknown/unlinked code just
     * falls through as-is and fails the normal way, so the error message
     * never reveals whether an ID exists.
     */
    private function resolveEmail(): string
    {
        $login = trim($this->string('email')->toString());

        if (str_contains($login, '@')) {
            return $login;
        }

        return Lecturer::where('code', $login)->whereNotNull('user_id')->first()?->user?->email ?? $login;
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = ['email' => $this->resolveEmail(), 'password' => $this->input('password')];

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
