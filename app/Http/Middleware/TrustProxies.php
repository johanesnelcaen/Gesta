<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Fideloper\Proxy\TrustProxies;

$middleware->trustProxies(
    '*', // ou ['*']
    Request::HEADER_X_FORWARDED_FOR | 
    Request::HEADER_X_FORWARDED_HOST |
    Request::HEADER_X_FORWARDED_PROTO |
    Request::HEADER_X_FORWARDED_PORT
);

