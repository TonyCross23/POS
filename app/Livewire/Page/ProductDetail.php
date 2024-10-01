<?php

namespace App\Livewire\Page;

use App\Models\Product;
use Livewire\Component;
use app\Helper\CartManagement;
use App\Livewire\Partials\Nav;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ProductDetail extends Component
{
    use LivewireAlert;

    public $slug;
    public $quantity = 1;

    public function mount($slug)
    {
        $this->slug = $slug;
    }

    //increaseQty
    public function increaseQty ()
    {
        $this->quantity++;
    }

    //desceaseQty
    public function decreaseQty ()
    {
        if($this->quantity > 1) {
            $this->quantity--;
        }
    }

    //add to cart submit
    public function addToCart ($product_id)
    {
        $total_count = CartManagement::addItemToCart($product_id);
        $this->dispatch('update_cart_count', total_count: $total_count)->to(Nav::class);

        //alert
        $this->alert('success', 'Add Cart Successfully',[
            'position' => 'bottom-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    public function render()
    {
        return view('livewire.page.product-detail',[
            'product' => Product::where('slug', $this->slug)->firstOrFail(),
        ]);
    }
}
