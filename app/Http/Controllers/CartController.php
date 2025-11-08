<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aidaily\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use Illuminate\Support\Collection;
use App\Models\Coupon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;

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

    /**
     * Applies the coupon code to the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function applyCoupon(Request $request)
    {
        $couponCode = $request->coupon_code;

        if(isset($couponCode)) {
            $subtotalValue = (float) str_replace(',', '', Cart::instance('cart')->subtotal());

            $coupon = Coupon::where('code', $couponCode)->where('expiry_date', '>=',Carbon::today())->where('cart_value', '<=', $subtotalValue)->first();

            if(!$coupon) {
                return redirect()->back()->with('error', 'Invalid or expired coupon code!');

            } else {
                Session::put('coupon', [
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'cart_value' => $coupon->cart_value
                ]);
                $this->calculateDiscountedTotal();
                return redirect()->back()->with('success', 'Coupon applied successfully!');
            }

        } else {
            return redirect()->back()->with('error', 'Invalid coupon code!');
        }

        return redirect()->back()->with('success', 'Coupon applied successfully!');
    }

    /**
     * Calculates the discount amount based on the stored coupon and cart subtotal.
     * The final discount value is stored in the session.
     * * @return void
     */
    public function calculateDiscountedTotal()
    {
        $discount = 0;
        if (Session::has('coupon')) {

            $coupon = Session::get('coupon');

            // We use the full, unformatted subtotal from the cart instance, then clean it.
            $rawSubtotal = Cart::instance('cart')->subtotal();
            $subtotalValue = (float) str_replace(',', '', $rawSubtotal);

            // Retrieve coupon value consistently
            $couponValue = $coupon['value'];

            if ($coupon['type'] == 'fixed') {
                // Fixed amount discount
                $discount = $couponValue;
            } else {
                // Percentage discount: Calculate X% of the subtotal
                // FIX: Use $subtotalValue (clean number) and $couponValue (clean number)
                $discount = ($subtotalValue * $couponValue) / 100;
            }

            // Ensure discount is not negative and doesn't exceed the subtotal
            $discount = max(0, min($discount, $subtotalValue));

            $subtotalAfterDiscount = $subtotalValue - $discount;

            // Recalculate tax on the discounted subtotal
            $taxAfterDiscount = ($subtotalAfterDiscount * config('cart.tax', 21)) / 100;
            $totalAfterDiscountAndTax = $subtotalAfterDiscount + $taxAfterDiscount;

            // Store the final calculated values for display
            Session::put('discounts', [
                // Use number_format to store cleanly formatted strings for display
                'discount' => number_format(floatval($discount), 2, '.', ''),
                'subtotal' => number_format(floatval($subtotalAfterDiscount), 2, '.', ''),
                'tax' => number_format(floatval($taxAfterDiscount), 2, '.', ''),
                'total' => number_format(floatval($totalAfterDiscountAndTax), 2, '.', ''),
            ]);
        }

        // IMPORTANT: If no coupon, ensure the old discount data is cleared
        if (!Session::has('coupon')) {
            Session::forget('discounts');
        }
    }

    /**
     * Removes the applied coupon and clears the discount from the session.
     *
     * @return \Illuminate\Http\Response
     */
    public function removeCoupon()
    {
        Session::forget('coupon');
        Session::forget('discounts');
        return redirect()->back()->with('success', 'Coupon removed successfully!');
    }
}
