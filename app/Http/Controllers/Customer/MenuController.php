<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->whereHas('products')
            ->orderBy('name')
            ->get();

        $selectedCategory = $categories->firstWhere('id', $request->integer('category')) ?? $categories->first();

        $selectedCategory?->load([
            'products' => fn ($query) => $query->orderBy('name'),
        ]);

        return view('customer.menu.index', [
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'cartCount' => array_sum(session('customer_cart', [])),
        ]);
    }
}
