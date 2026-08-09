<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_category_index(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->get(route('admin.categories.index'))
            ->assertOk()
            ->assertSee('Categories');
    }

    public function test_authenticated_user_can_view_product_create_page(): void
    {
        $user = User::factory()->admin()->create();
        $category = Category::create([
            'name' => 'Main Courses',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('admin.products.create'))
            ->assertOk()
            ->assertSee('Add Product')
            ->assertSee($category->name);
    }

    public function test_authenticated_user_can_create_a_product(): void
    {
        $user = User::factory()->admin()->create();
        $category = Category::create([
            'name' => 'Main Courses',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Chicken Adobo',
            'description' => 'Classic Filipino chicken dish',
            'price' => '180.00',
            'stock' => 12,
            'is_available' => '1',
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'category_id' => $category->id,
            'name' => 'Chicken Adobo',
            'stock' => 12,
            'is_available' => 1,
        ]);
    }

    public function test_product_creation_requires_the_required_fields(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->from(route('admin.products.create'))
            ->post(route('admin.products.store'), [])
            ->assertRedirect(route('admin.products.create'))
            ->assertSessionHasErrors(['category_id', 'name', 'price', 'stock']);
    }

    public function test_category_with_products_requires_force_confirmation_before_deletion(): void
    {
        $user = User::factory()->admin()->create();
        $category = Category::create(['name' => 'Main Courses', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Chicken Adobo',
            'price' => '180.00',
            'stock' => 12,
            'is_available' => true,
        ]);

        $this->actingAs($user)
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_force_deleting_category_removes_its_products_and_images(): void
    {
        Storage::fake('public');

        $user = User::factory()->admin()->create();
        $category = Category::create(['name' => 'Main Courses', 'is_active' => true]);
        $imagePath = 'products/chicken-adobo.png';
        Storage::disk('public')->put($imagePath, 'image-content');
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Chicken Adobo',
            'price' => '180.00',
            'stock' => 12,
            'image' => $imagePath,
            'is_available' => true,
        ]);

        $this->actingAs($user)
            ->delete(route('admin.categories.destroy', $category), ['force_delete_products' => true])
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('success', 'Category and its products deleted successfully.');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing($imagePath);
    }

    public function test_category_with_products_in_existing_orders_cannot_be_force_deleted(): void
    {
        $user = User::factory()->admin()->create();
        $category = Category::create(['name' => 'Main Courses', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Chicken Adobo',
            'price' => '180.00',
            'stock' => 12,
            'is_available' => true,
        ]);
        $order = Order::create([
            'order_number' => '0001',
            'customer_name' => 'Juan Dela Cruz',
            'total_amount' => '180.00',
            'status' => 'accepted',
            'payment_status' => 'unpaid',
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => '180.00',
            'subtotal' => '180.00',
        ]);

        $this->actingAs($user)
            ->delete(route('admin.categories.destroy', $category), ['force_delete_products' => true])
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}
