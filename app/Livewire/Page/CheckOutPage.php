<?php

namespace App\Livewire\Page;

use Stripe\Stripe;
use App\Models\Order;
use App\Models\Address;
use Livewire\Component;
use Stripe\Checkout\Session;
use app\Helper\CartManagement;

#[Title('Check out - Prime Shop')]
class CheckOutPage extends Component
{
    public $first_name;
    public $last_name;
    public $phone;
    public $street_address;
    public $city;
    public $state;
    public $zip_code;
    public $payment_method;

    public function mount ()
    {
        $cart_item = CartManagement::getCartItemsFromCookie();
        if(count($cart_item) == 0) {
            return redirect()->route('products');
        }
    }

    public function order ()
    {
        $this->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'street_address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip_code' => 'required',
            'payment_method' => 'required',
        ]);

        $cart_item = CartManagement::getCartItemsFromCookie();

        $line_items = [];

        foreach ($cart_item as $item)
        {
            $line_items[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => $item['unit_amount'] * 100,
                    'product_data' => [
                        'name' => $item['name'],
                    ]
                    ],
                    'quantity' => $item['quantity'],
                ];
        }

        $order = new Order();
        $order->user_id = auth()->user()->id;
        $order->grand_total = CartManagement::calculateGrandTotal($cart_item);
        $order->payment_method = $this->payment_method;
        $order->payment_status = 'pending';
        $order->status = 'new';
        $order->currency = 'usd';
        $order->shipping_amount= 0;
        $order->shipping_method = 'none';
        $order->notes = 'Order placed by'. auth()->user()->id;

        $address = new Address();
        $address->first_name = $this->first_name;
        $address->last_name = $this->last_name;
        $address->phone = $this->phone;
        $address->street_address = $this->street_address;
        $address->state = $this->state;
        $address->zip_code = $this->zip_code;

        $redirect_url = "";

        if($this->payment_method == 'stripe')
        {
            Stripe::setApiKey(env('STRIPE_KEY'));
            $sessionCheckout = Session::create([
                'payment_method_types' => ['card'],
                'customer_email' => auth()->user()->email,
                'line_items' => $line_items,
                'mode' => 'payment',
                'success_url' => route('success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('cancel')
            ]);
            $redirect_url = $sessionCheckout->url;
        }else {
            $redirect_url = route('success');
        }

        $order->save();
        $address->order_id = $order->id;
        $address->save();
        $order->items()->createMany($cart_item);
        CartManagement::clearCartItems();
        return redirect($redirect_url);

    }

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
