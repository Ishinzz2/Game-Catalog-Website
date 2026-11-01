<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login(): mixed
    {
        $credentials = $this->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($this->email).'|'.request()->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', "Terlalu banyak percobaan masuk. Coba lagi dalam {$seconds} detik.");

            return null;
        }

        if (! Auth::attempt($credentials, $this->remember)) {
            RateLimiter::hit($throttleKey, 60);
            $this->addError('email', 'Email atau kata sandi tidak sesuai.');

            return null;
        }

        RateLimiter::clear($throttleKey);
        request()->session()->regenerate();

        return $this->redirectRoute('dashboard', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.guest');
    }
}
