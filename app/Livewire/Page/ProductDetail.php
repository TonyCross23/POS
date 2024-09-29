<?php

namespace App\Livewire\Page;

use App\Models\Product;
use Livewire\Component;

class ProductDetail extends Component
{
    public $slug;

    public function mount($slug)
    {
        $this->slug = $slug;
    }

    public function render()
    {
        return view('livewire.page.product-detail',[
            'product' => Product::where('slug', $this->slug)->firstOrFail(),
        ]);
    }
}
