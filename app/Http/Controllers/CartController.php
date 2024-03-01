<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cart = Cart::instance(Auth::user()->id)->content();

        $total = 0;

        foreach ($cart as $c){
            $total += $c->qty * $c->price;
        }

        return view('carts.index', compact('cart', 'total'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Cart::instance(Auth::user()->id)->add(
            [
                'id' => $request->id,
                'name' => $request->name,
                'price' => $request->price,
                'qty' => $request->qty,
                'weight' => $request->weight,
                'options' => [
                    'image' => $request->image,
                ]
            ]
            );

            return to_route('products.show', $request->get('id'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $user_shoppingcarts = DB::table('shopping_cart')->where('instance', Auth::user()->id)->get();
        $count = $user_shoppingcarts->count();

        $count += 1;
        Cart::instance(Auth::user()->id)->store($count);

        DB::table('shopping_cart')->where('instance', Auth::user()->id)->where('number', null)->update(['number' => $count, 'buy_flag' => true]);

        Cart::instance(Auth::user()->id)->destroy();

        return to_route('carts.index');
    }
}
