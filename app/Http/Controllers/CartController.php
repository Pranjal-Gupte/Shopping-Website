<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aidaily\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use Illuminate\Support\Collection;

class CartController extends Controller
{
    public function index()
    {
        // Restore the cart if the user is logged in
        if (Auth::check()) {
            Cart::instance('cart')->restore(Auth::id());
        }

        // We'll get the items to display from the cart instance
        $items = Cart::instance('cart')->content();
        
        $cartItemsWithProducts = new Collection();
        $subtotal = 0;
        $tax = 0;
        $total = 0;
        
        // Retrieve the tax rate from the configuration file
        // and convert to a decimal for calculation
        $taxRate = config('cart.tax', 21) / 100; // If config\cart.php fails to retrieve the tax rate, then 21 will set as default

        foreach ($items as $item) {
            $product = Product::find($item->id);
            if ($product) {
                // Clean the price string before casting to a float
                $priceString = preg_replace('/[^0-9.]/', '', $product->regular_price);
                $itemPrice = (float) $priceString;
                $itemSubtotal = $itemPrice * $item->qty;
                $itemTax = ($itemSubtotal * $taxRate); 

                $subtotal += $itemSubtotal;
                $tax += $itemTax;
                $total += $itemSubtotal + $itemTax;

                $cartItemsWithProducts->push((object) [
                    'rowId' => $item->rowId,
                    'id' => $item->id,
                    'name' => $item->name,
                    'image' => $product->image,
                    'qty' => $item->qty,
                    'price' => $itemPrice,
                    'subtotal' => $itemSubtotal,
                ]);
            }
        }
        // Round the final tax amount
        $tax = round($tax);

        // Round the final total to a whole number for easier cash payments
        $total = round($total);

        return view('cart', compact('cartItemsWithProducts', 'subtotal', 'tax', 'total'));
    }

    public function addToCart(Request $request)
    {
        $product = Product::find($request->id);
        if ($product) {
            $cartItem = Cart::instance('cart')->add(
                $request->id,
                $request->name,
                $request->quantity,
                $product->regular_price
            );
            $cartItem->setTaxRate(config('cart.tax', 21));
            
            if ($userId = Auth::id()) {
                Cart::instance('cart')->store($userId);
            }
        }
        
        return redirect()->back();
    }

    public function increaseCartQuantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId, $qty);
        
        if (Auth::check() && Auth::id()) {
            Cart::instance('cart')->store(Auth::id());
        }
        return redirect()->back();
    }

    public function decreaseCartQuantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty - 1;
        Cart::instance('cart')->update($rowId, $qty);
        
        if (Auth::check() && Auth::id()) {
            Cart::instance('cart')->store(Auth::id());
        }
        return redirect()->back();
    }

    public function removeItem($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        
        if (Auth::check() && Auth::id()) {
            Cart::instance('cart')->store(Auth::id());
        }
        return redirect()->back();
    }

    public function clearCart()
    {
        Cart::instance('cart')->destroy();
        
        if (Auth::check()) {
            Cart::instance('cart')->store(Auth::id());
        }
        return redirect()->back();
    }
}
