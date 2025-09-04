<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Aidaily\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Session;
use App\Models\Product;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * The user has been authenticated.
     * This is the place to restore the shopping cart from the database
     * and merge it with a potential guest cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Get the current cart instance
        $guestCart = Cart::instance('cart')->content();

        // Check if a user has a saved cart
        $userCart = Cart::instance('cart')->restore($user->id);

        // If the user has a cart, merge the guest cart with it
        if ($userCart) {
            $userCart->merge($guestCart);
        } else {
            // If the user does not have a saved cart, use the guest cart as the user's cart
            $userCart = $guestCart;
        }

        // Add any guest cart items to the user's cart
        foreach ($userCart as $item) {
            $product = Product::find($item->id);
            if ($product) {
                 $cartItem = Cart::instance('cart')->add(
                    $item->id,
                    $item->name,
                    $item->qty,
                    (float)preg_replace('/[^0-9.]/', '', $product->regular_price),
                    ['product_id' => $product->id]
                );
                $cartItem->setTaxRate(config('cart.tax', 21));
            }
        }
        
        // Save the updated cart to the database
        Cart::instance('cart')->store($user->id);
    }
    
    public function logout(Request $request)
    {
        Cart::instance('cart')->store(Auth::id());
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->loggedOut($request) ?: redirect('/');
    }
}
