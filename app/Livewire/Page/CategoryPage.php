<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\Category;
use Livewire\Attributes\Title;

class CategoryPage extends Component
{
    #[Title('Categories - Prime Shop')]
    public function render()
    {
        return view('livewire.page.category',[
            'categories' => Category::where('is_active', 1)->get(),
        ]);
    }
}
