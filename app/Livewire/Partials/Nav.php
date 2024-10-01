<?php

namespace App\Livewire\Partials;

use Livewire\Component;
use Livewire\Attributes\On;
use app\Helper\CartManagement;

class Nav extends Component
{
    public $total_count = 0;

    //cart count on nav click card from add to cart items
    public function mount()
    {
        $this->total_count = count(CartManagement::getCartItemsFromCookie());
    }

    #[On('update_cart_count')]
    public function updateCartCount ($total_count)
    {
        $this->total_count = $total_count;
    }

    public function render()
    {
        return view('livewire.partials.nav');
    }
}
