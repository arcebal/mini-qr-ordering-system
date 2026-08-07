<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerSessionController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->session()->has('customer_name')) {
            return redirect()->route('customer.menu');
        }

        return view('customer.start');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
        ]);

        $request->session()->put('customer_name', trim($validated['customer_name']));

        return redirect()->intended(route('customer.menu'));
    }
}
