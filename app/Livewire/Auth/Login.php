<?php
namespace App\\Livewire\\Auth;
use Illuminate\\Support\\Facades\\Auth;
use Livewire\\Component;
class Login extends Component {
    public string $email='';
    public string $password='';
    public bool $remember=false;
    public function login(): mixed {
        $credentials=$this->validate(['email'=>['required','email'],'password'=>['required','string']]);
        if (!Auth::attempt($credentials,$this->remember)) {
            $this->addError('email','Email atau kata sandi tidak sesuai.');
            return null;
        }
        request()->session()->regenerate();
        return $this->redirectRoute('dashboard',navigate:true);
    }
    public function render() { return view('livewire.auth.login')->layout('layouts.guest'); }
}
