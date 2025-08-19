<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:owners');

        // ログインしたオーナーIDとショップのオーナーが同じかどうかチェックして、別のオーナーのショップを開こうとしたら、404になるように調整
        $this->middleware(function($request, $next){
            $id = $request->route()->parameter('shop'); //shopのid取得
            if(!is_null($id)){ // null判定（indexページはパラメータがないので、not nullで判別）
                $shopsOwnerId = Shop::findOrFail($id)->owner->id;
                $shopId = (int)$shopsOwnerId; // キャスト 文字列→数値に型変換
                $ownerId = Auth::id();
                if($shopId !== $ownerId){ // 同じでなかったら
                    abort(404); // 404画面表示
                }
            }
            return $next($request);
        });
    }

    public function index()
    {
//        $ownerId = Auth::id();
        $shops = Shop::where('owner_id', Auth::id())->get();

        return view('owner.shops.index', compact('shops'));
    }

    public function edit($id)
    {
        dd(Shop::findOrFail($id));
    }

    public function update(Request $request, $id)
    {

    }
}
