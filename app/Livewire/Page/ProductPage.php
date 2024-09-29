<?php

namespace App\Livewire\Page;

use App\Models\Brand;
use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

class ProductPage extends Component
{
    use WithPagination;
    #[Title('Products - Prime Shop')]

    public function render()
    {
        return view('livewire.page.product',[
            'products' => Product::query()->where('is_active', 1)->paginate(9),
            'categories' => Category::where('is_active', 1)->get(),
            'brands' => Brand::where('is_active', 1)->get(),
        ]);
    }
}
