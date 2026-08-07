<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerName
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('customer_name')) {
            if ($request->isMethod('get')) {
                $request->session()->put('url.intended', $request->fullUrl());
            }

            return redirect()->route('customer.start')
                ->with('warning', 'Please enter your name before ordering.');
        }

        return $next($request);
    }
}
