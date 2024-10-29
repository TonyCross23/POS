<?php

namespace App\Livewire\Page;

use App\Models\Order;
use App\Models\Address;
use Livewire\Component;
use App\Models\OrderItem;
use Livewire\Attributes\Title;

#[Title('Oreder Detail - Prime Shop')]
class MyOrdersDetailPage extends Component
{
    public $order_id;

    public function mount($order_id) {
        $this->order_id = $order_id;
    }

    public function render()
    {
        $order_items = OrderItem::with('product')->where('order_id',$this->order_id)->get();
        $address = Address::where('order_id',$this->order_id)->first();
        $order = Order::where('id',$this->order_id)->first();

        return view('livewire.page.my-orders-detail-page',[
            'order_items' => $order_items,
            'address' => $address,
            'order' => $order,
        ]);
    }
}
