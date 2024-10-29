<?php

namespace App\Livewire\Page;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('My Order - Prime Shop')]
class MyOrder extends Component
{
    use WithPagination;

    public function render()
    {

        $my_order = Order::where('user_id', auth()->user()->id)->latest()->paginate(5);

        return view('livewire.page.my-order',[
            'orders' => $my_order,
        ]);
    }
}
