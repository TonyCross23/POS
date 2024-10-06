<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Password;

#[Title('Forgot password - Prime Shop')]
class ForgotPage extends Component
{
    public $email;

    public function save ()
    {
        $this->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::sendResetLink(['email' => $this->email]);

        if($status === Password::RESET_LINK_SENT)
        {
            session()->flash('success','Password reset link has been sent to your email address!');
            $this->email='';
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-page');
    }
}
