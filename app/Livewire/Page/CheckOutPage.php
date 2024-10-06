<?php

namespace App\Livewire\Page;

use Livewire\Component;
use app\Helper\CartManagement;

#[Title('Check out - Prime Shop')]
class CheckOutPage extends Component
{
    public function render()
    {
        $cart_item = CartManagement::getCartItemsFromCookie();
        $grand_total = CartManagement::calculateGrandTotal($cart_item);

        return view('livewire.page.check-out-page',[
            'cart_item' => $cart_item,
            'grand_total' => $grand_total,
        ]);
    }
}
