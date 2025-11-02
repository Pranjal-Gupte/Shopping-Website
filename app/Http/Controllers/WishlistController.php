<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aidaily\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index() 
    {
        // Restore the wishlist if the user is logged in
        if (Auth::check()) {
            Cart::instance('wishlist')->restore(Auth::id());
        }
        $items = Cart::instance('wishlist')->content();
        return view('wishlist', compact('items'));
    }

    public function addToWishlist(Request $request)
    {   
        // Using a more specific method call to match our CartController for consistency
        Cart::instance('wishlist')->add(
            $request->id, 
            $request->name, 
            $request->quantity, 
            $request->price
        )->associate('App\Models\Product');

        // Storing the updated wishlist to the database
        if ($userId = Auth::id()) {
            Cart::instance('wishlist')->store($userId);
        }
        return redirect()->back();
    }

    public function removeFromWishlist($rowId)
    {   
        // Storing the updated (removed) wishlist to the database
        if($userId = Auth::id()) {
            Cart::instance('wishlist')->store($userId);
        }
        Cart::instance('wishlist')->remove($rowId);
        return redirect()->back();
    }

    public function clearWishlist()
    {
        // Storing the destroyed (empty) wishlist to the database
        if($userId = Auth::id()) {
            Cart::instance('wishlist')->store($userId);
        }
        Cart::instance('wishlist')->destroy();
        return redirect()->back();
    }
}
