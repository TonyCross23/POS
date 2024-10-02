<?php

namespace App\Livewire\Page;

use Livewire\Component;
use app\Helper\CartManagement;
use App\Livewire\Partials\Nav;

class Cart extends Component
{
    public $cart_items = [];
    public $grand_total;

    public function mount ()
    {
        $this->cart_items = CartManagement::getCartItemsFromCookie();
        $this->grand_total = CartManagement::calculateGrandTotal($this->cart_items);
    }

    //item remove
    public function removeItem ($product_id)
    {
        $this->cart_items = CartManagement::removeCartItem($product_id);
        $this->grand_total = CartManagement::calculateGrandTotal($this->cart_items);
        $this->dispatch('update_cart_count',total_count: count($this->cart_items))->to(Nav::class);
    }

    //increaseQty
    public function increaseQty ($product_id)
    {
        $this->cart_items = CartManagement::incrementQuantityToCartItem($product_id);
        $this->grand_total = CartManagement::calculateGrandTotal($this->cart_items);
    }

        //decreaseQty
        public function decreaseQty ($product_id)
        {
            $this->cart_items = CartManagement::decrementQuantityToCartItem($product_id);
            $this->grand_total = CartManagement::calculateGrandTotal($this->cart_items);
        }

    public function render()
    {
        return view('livewire.page.cart');
    }
}
