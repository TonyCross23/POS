<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Title;

class LoginPage extends Component
{
    public $email;
    public $password;

    #[Title('Login - Prime Shop')]

    public function save ()
    {
        $this->validate([
            'email' => 'required|max:255|exists:users,email',
            'password' => 'required|max:255',
        ]);

        if(!auth()->attempt(['email' => $this->email, 'password' => $this->password ]))
        {
            session()->flash('error', 'Invalid Credentials');
            return;
        }

        return redirect()->intended();
    }

    public function render()
    {
        return view('livewire.auth.login-page');
    }
}
