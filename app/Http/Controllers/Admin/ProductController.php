<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Services\ProductImageService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private readonly ProductImageService $images)
    {
    }

    public function index()
    {
        $products = Product::with('category')
            ->latest()
            ->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
{
    $request->validate([
        'category_id' => 'required|exists:categories,id',
        'name' => 'required|max:255',
        'description' => 'nullable',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $imagePath = $request->hasFile('image')
        ? $this->images->upload($request->file('image'))
        : null;

    Product::create([

        'category_id' => $request->category_id,

        'name' => $request->name,

        'description' => $request->description,

        'price' => $request->price,

        'stock' => $request->stock,

        'image' => $imagePath,

        'is_available' => $request->has('is_available'),

    ]);

    return redirect()
        ->route('admin.products.index')
        ->with('success', 'Product added successfully.');
}

    public function edit(Product $product)
{
    $categories = Category::where('is_active', true)
        ->orderBy('name')
        ->get();

    return view('admin.products.edit', compact('product', 'categories'));
}

    public function update(Request $request, Product $product)
{
    $request->validate([
        'category_id' => 'required|exists:categories,id',
        'name' => 'required|max:255',
        'description' => 'nullable',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $imagePath = $product->image;

    if ($request->hasFile('image')) {

        $this->images->delete($product->image);
        $imagePath = $this->images->upload($request->file('image'));
    }

    $product->update([
        'category_id' => $request->category_id,
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
        'stock' => $request->stock,
        'image' => $imagePath,
        'is_available' => $request->has('is_available'),
    ]);

    return redirect()
        ->route('admin.products.index')
        ->with('success', 'Product updated successfully.');
}

    public function destroy(Product $product)
{
    $this->images->delete($product->image);

    $product->delete();

    return redirect()
        ->route('admin.products.index')
        ->with('success', 'Product deleted successfully.');
}
}
