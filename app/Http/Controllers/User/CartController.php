<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\User;
use App\Models\Stock;
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

        foreach($products as $product) {
            // 在庫確認し決済前に在庫を減らしておく
            $quantity = Stock::where('product_id', $product->id)->sum('quantity');

            if ($product->pivot->quantity > $quantity) {
                // 在庫が足りないので、買えないので、indexに戻す
                return view('user.cart.index');
            } else {
                // stripeに渡す内容は決まっているので、詳細はstripeのサイトで確認（新しいVerだと記述方法が変わったみたい）
                $line_item[] = [
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $product->name,
                            'description' => $product->information,
                        ],
                        'unit_amount' => $product->price,
                    ],
                    'quantity' => $product->pivot->quantity,
                ];
                array_push($line_items, $line_item);
            }
        }

//        dd($line_items);

        // 決済処理の前に購入された分の在庫を減らしておく
        foreach ($products as $product) {
            Stock::create([
                'product_id' => $product->id,
                'type' => \Constant::PRODUCT_LIST['reduce'],
                'quantity' => $product->pivot->quantity * -1
            ]);
        }

//        dd('test');

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => $line_items,
            'mode' => 'payment',
            'success_url' => route('user.cart.success'),
            'cancel_url' => route('user.cart.cancel'),
        ]);

        $publicKey = env('STRIPE_PUBLIC_KEY');

        return view('user.checkout', compact('checkout_session','publicKey'));
    }

    public function success() {
        // 決済成功したら、カートを空にしてやる
        Cart::where('user_id', Auth::id())->delete();

        // 商品一覧へ戻す
        return redirect()->route('user.items.index');
    }

    public function cancel() {
        // 決済キャンセルされたら、決済前に減らしていた在庫を元に戻す必要がある
        $user = User::findOrFail(Auth::id());

        foreach($user->products as $product) {
            Stock::create([
                'product_id' => $product->id,
                'type' => \Constant::PRODUCT_LIST['add'],
                'quantity' => $product->pivot->quantity
            ]);
        }

        // カートに戻す
        return redirect()->route('user.cart.index');
    }
}
