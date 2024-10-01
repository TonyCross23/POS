<?php

namespace app\Helper;

use App\Models\Product;
use Illuminate\Support\Facades\Cookie;

class CartManagement
{
    //add items to cart
    public static function addItemToCart ($product_id)
    {
        $cart_items = self::getCartItemsFromCookie();

        $existing_item = null;

        foreach ($cart_items as $key => $item) {
            if($item['product_id'] == $product_id)
            {
                $existing_item = $key;
                break;
            }
        }

        if($existing_item !== null)
        {
            $cart_items[$existing_item]['quantity']++;
            $cart_items[$existing_item]['total_amount'] = $cart_items[$existing_item]['quantity']*$cart_items[$existing_item]['unit_amount'];
        }else {
            $product = Product::where('id',$product_id)->first(['id','name','image','price']);

            if($product)
            {
                $cart_items[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'image' => $product->image[0],
                    'quantity' => 1,
                    'price' => $product->price,
                    'unit_amount' => $product->price,
                    'total_amount' => $product->price,
                ];
            }
        }
        self::addCartItemsToCookie($cart_items);
        return count($cart_items);
    }

    //add cart items to cookies
    public static function addCartItemsToCookie ($cart_items)
    {
        Cookie::queue('cart_items', json_encode($cart_items), 60*24*30);
    }

    //clear cart items to cookies
    public static function clearCartItems ()
    {
        Cookie::queue(Cookie::forget('cart_items'));
    }

    //get all items from cookies
    public static function getCartItemsFromCookie ()
    {
        $cart_items = json_decode(Cookie::get('cart_items'), true);

        if(!$cart_items)
        {
            $cart_items = [];
        }

        return $cart_items;
    }
}
