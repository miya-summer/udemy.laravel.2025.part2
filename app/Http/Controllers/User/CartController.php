<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $user = User::findOrFail(Auth::id());
        $products = $user->products; // 多対多のリレーション
        $totalPrice = 0;

        foreach($products as $product){
            $totalPrice += $product->price * $product->pivot->quantity;
        }

//        dd($products, $totalPrice);

        return view('user.cart', compact('products', 'totalPrice'));
    }

    public function add(Request $request){
        $itemInCart = Cart::where('product_id', $request->product_id)
            ->where('user_id', Auth::id())
            ->first(); //カートに商品があるか確認

        if($itemInCart){
            $itemInCart->quantity += $request->quantity; //あれば数量を追加
            $itemInCart->save();
        } else {
            Cart::create([ // なければ新規作成
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity
            ]);
        }

        return redirect()->route('user.cart.index');
    }

    public function delete($id)
    {
        Cart::where('product_id', $id)
            ->where('user_id', Auth::id())->delete();

        return redirect()->route('user.cart.index');
    }

    public function checkout()
    {
        $user = User::findOrFail(Auth::id());
        $products = $user->products; // 多対多のリレーション
        $line_items = [];

        foreach($products as $product){
            // stripeに渡す内容は決まっているので、詳細はstripeのサイトで確認
            $line_item = [
                'name' => $product->name,
                'description' => $product->information,
                'amount' => $product->price,
                'currency' => 'jpy',
                'quantity' => $product->pivot->quantity,
            ];
            array_push($line_items, $line_item);
        }

//        dd($line_items);

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => [$line_items],
            'mode' => 'payment',
            'success_url' => route('user.items.index'),
            'cancel_url' => route('user.cart.index'),
        ]);

        $publicKey = env('STRIPE_SECRET_KEY');

        return view('user.checkout', compact('checkout_session','publicKey'));
    }
}
