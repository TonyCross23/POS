<?php

namespace App\Livewire\Page;

use App\Models\Brand;
use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

class ProductPage extends Component
{
    use WithPagination;
    #[Title('Products - Prime Shop')]

    #[Url]
    public $selected_categories = [];

    #[Url]
    public $selected_brands = [];

    #[Url]
    public $featured;

    #[Url]
    public $on_sale;

    #[Url]
    public $price_range = 300000;

    #[Url]
    public $sort = 'latest';

    public function render()
    {
        $productQuery = Product::query()->where('is_active', 1);

        if(!empty($this->selected_categories))
        {
            $productQuery->whereIn('category_id',$this->selected_categories);
        }

        if(!empty($this->selected_brands))
        {
            $productQuery->whereIn('brand_id',$this->selected_brands);
        }

        if($this->featured)
        {
            $productQuery->where('is_featured', 1);
        }

        if($this->on_sale)
        {
            $productQuery->where('on_sale', 1);
        }

        if($this->price_range)
        {
            $productQuery->whereBetween('price',[0, $this->price_range]);
        }

        if($this->sort == "latest")
        {
            $productQuery->latest();
        }

        if($this->sort == "price")
        {
            $productQuery->orderBy('price');
        }

        return view('livewire.page.product',[
            'products' => $productQuery->paginate(6),
            'categories' => Category::where('is_active', 1)->get(),
            'brands' => Brand::where('is_active', 1)->get(),
        ]);
    }
}
