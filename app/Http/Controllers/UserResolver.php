<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use OwenIt\Auditing\Contracts\UserResolver as Resolver;

class UserResolver implements Resolver
{
    public static function resolve()
    {
        // Sua lógica personalizada
        return Session::get('user');
    }
}