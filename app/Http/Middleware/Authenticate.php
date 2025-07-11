<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Route;

class Authenticate extends Middleware
{
    protected $user_route = 'user.login';   //user.login => RouteServiceProvider.phpのRoute::prefix('/')->as('user.')のasの部分
    protected $owner_route = 'owner.login'; //owner.login => RouteServiceProvider.phpのRoute::prefix('owner')->as('owner.')のasの部分
    protected $admin_route = 'admin.login'; //admin.login => RouteServiceProvider.phpのRoute::prefix('admin')->as('admin.')のasの部分

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            if (Route::is('owner.*')) {
                return route($this->owner_route);
            } elseif (Route::is('admin.*')) {
                return route($this->admin_route);
            } else {
                return route($this->user_route);
            }
        }
    }
}
