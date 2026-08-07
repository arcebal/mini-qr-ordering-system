<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::withCount('products')->latest()->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255|unique:categories,name',
            'description' => 'nullable|max:500',
        ]);

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing a category.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|max:500',
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Request $request, Category $category)
    {
        if ($category->products()->whereHas('orderItems')->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'This category cannot be deleted because its products are included in existing orders.');
        }

        if ($category->products()->exists() && ! $request->boolean('force_delete_products')) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'This category has products. Confirm the deletion to remove the category and its products.');
        }

        $productCount = $category->products()->count();

        DB::transaction(function () use ($category) {
            $category->products()->each(function ($product) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }

                $product->delete();
            });

            $category->delete();
        });

        $message = $productCount > 0
            ? 'Category and its products deleted successfully.'
            : 'Category deleted successfully.';

        return redirect()
            ->route('admin.categories.index')
            ->with('success', $message);
    }
}
