<?php

namespace App\Livewire\Page;

use App\Models\Brand;
use Livewire\Component;
use App\Models\Category;
use Livewire\Attributes\Title;

class HomePage extends Component
{
    #[Title('Home page - Prime Shop')]
    public function render()
    {
        return view('livewire.page.home-page',[
            'brands' => Brand::where('is_active', 1)->get(),
            'categories' => Category::where('is_active', 1)->get(),
        ]);
    }
}
