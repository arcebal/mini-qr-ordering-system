<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_browse_available_and_out_of_stock_menu_items(): void
    {
        $category = Category::create(['name' => 'Meals', 'is_active' => true]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Chicken Adobo',
            'price' => '180.00',
            'stock' => 5,
            'is_available' => true,
        ]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Pork Sisig',
            'price' => '190.00',
            'stock' => 0,
            'is_available' => true,
        ]);

        $this->withSession(['customer_name' => 'Juan Dela Cruz'])->get(route('customer.menu'))
            ->assertOk()
            ->assertSee('Chicken Adobo')
            ->assertSee('Pork Sisig')
            ->assertSee('Out of Stock')
            ->assertDontSee('Order status');
    }

    public function test_menu_category_button_shows_only_the_selected_category_products(): void
    {
        $meals = Category::create(['name' => 'Meals', 'is_active' => true]);
        $drinks = Category::create(['name' => 'Drinks', 'is_active' => true]);
        Product::create(['category_id' => $meals->id, 'name' => 'Chicken Adobo', 'price' => '180.00', 'stock' => 5, 'is_available' => true]);
        Product::create(['category_id' => $drinks->id, 'name' => 'Calamansi Juice', 'price' => '55.00', 'stock' => 5, 'is_available' => true]);

        $this->withSession(['customer_name' => 'Juan Dela Cruz'])->get(route('customer.menu', ['category' => $drinks->id]))
            ->assertOk()
            ->assertSee('Meals')
            ->assertSee('Drinks')
            ->assertSee('Calamansi Juice')
            ->assertDontSee('Chicken Adobo');
    }

    public function test_guests_can_add_and_adjust_available_products_in_their_cart(): void
    {
        $product = $this->product(['stock' => 2]);

        $this->withSession(['customer_name' => 'Juan Dela Cruz'])->post(route('customer.cart.add', $product))
            ->assertRedirect()
            ->assertSessionHas('customer_cart.'.$product->id, 1);

        $this->patch(route('customer.cart.update', $product), ['operation' => 'increase'])
            ->assertRedirect()
            ->assertSessionHas('customer_cart.'.$product->id, 2);

        $this->patch(route('customer.cart.update', $product), ['operation' => 'decrease'])
            ->assertRedirect()
            ->assertSessionHas('customer_cart.'.$product->id, 1);
    }

    public function test_unavailable_products_cannot_be_added_to_cart(): void
    {
        $product = $this->product(['stock' => 0]);

        $this->withSession(['customer_name' => 'Juan Dela Cruz'])->post(route('customer.cart.add', $product))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertNull(session('customer_cart.'.$product->id));
    }

    public function test_guest_can_place_an_order_with_only_their_name(): void
    {
        $product = $this->product(['price' => '180.00', 'stock' => 5]);

        $response = $this->withSession(['customer_cart' => [$product->id => 2], 'customer_name' => 'Juan Dela Cruz'])
            ->post(route('customer.checkout.place'));

        $response->assertRedirect(route('customer.order-success', 1));
        $this->assertDatabaseHas('orders', [
            'id' => 1,
            'order_number' => '0001',
            'customer_name' => 'Juan Dela Cruz',
            'status' => 'accepted',
            'payment_method' => 'counter',
            'payment_status' => 'unpaid',
        ]);
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
        $this->assertEmpty(session('customer_cart', []));
    }

    public function test_customer_can_place_an_order_with_mock_online_payment(): void
    {
        $product = $this->product(['price' => '180.00', 'stock' => 5]);

        $this->withSession(['customer_cart' => [$product->id => 1], 'customer_name' => 'Juan Dela Cruz'])
            ->post(route('customer.checkout.place'), ['payment_method' => 'mock_online'])
            ->assertRedirect(route('customer.order-success', 1));

        $this->assertDatabaseHas('orders', [
            'id' => 1,
            'payment_method' => 'mock_online',
            'payment_status' => 'paid',
            'status' => 'accepted',
        ]);
    }

    public function test_placing_an_order_deducts_stock_and_marks_sold_out_product_unavailable(): void
    {
        $product = $this->product(['stock' => 2]);

        $this->withSession(['customer_cart' => [$product->id => 2], 'customer_name' => 'Juan Dela Cruz'])
            ->post(route('customer.checkout.place'))
            ->assertRedirect(route('customer.order-success', 1));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 0,
            'is_available' => false,
        ]);
    }

    public function test_placing_an_order_keeps_product_available_when_stock_remains(): void
    {
        $product = $this->product(['stock' => 5]);

        $this->withSession(['customer_cart' => [$product->id => 2], 'customer_name' => 'Juan Dela Cruz'])
            ->post(route('customer.checkout.place'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 3,
            'is_available' => true,
        ]);
    }

    public function test_order_is_not_created_when_cart_quantity_exceeds_current_stock(): void
    {
        $product = $this->product(['stock' => 1]);

        $this->withSession(['customer_cart' => [$product->id => 2], 'customer_name' => 'Juan Dela Cruz'])
            ->from(route('customer.checkout'))
            ->post(route('customer.checkout.place'))
            ->assertRedirect(route('customer.checkout'))
            ->assertSessionHasErrors('cart');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 1,
            'is_available' => true,
        ]);
    }

    public function test_customer_must_enter_a_name_before_browsing_the_menu(): void
    {
        $this->get(route('customer.menu'))
            ->assertRedirect(route('customer.start'));

        $this->post(route('customer.start.store'), ['customer_name' => 'Juan Dela Cruz'])
            ->assertRedirect(route('customer.menu'))
            ->assertSessionHas('customer_name', 'Juan Dela Cruz');
    }

    public function test_customer_can_view_only_the_status_of_their_own_order(): void
    {
        $order = \App\Models\Order::create([
            'order_number' => '0001',
            'customer_name' => 'Juan Dela Cruz',
            'total_amount' => '180.00',
            'status' => 'preparing',
            'payment_status' => 'unpaid',
        ]);

        $this->withSession(['customer_name' => 'Juan Dela Cruz', 'customer_order_ids' => [$order->id]])
            ->get(route('customer.order-status', $order))
            ->assertOk()
            ->assertJsonPath('status', 'preparing')
            ->assertJsonPath('payment_status', 'unpaid')
            ->assertJsonPath('payment_method', 'counter');

        app('session')->flush();

        $this->withSession(['customer_name' => 'Another Customer', 'customer_order_ids' => []])
            ->get(route('customer.order-status', $order))
            ->assertForbidden();
    }

    public function test_customer_navigation_links_to_the_latest_order_status(): void
    {
        $olderOrder = Order::create([
            'order_number' => '0001',
            'customer_name' => 'Juan Dela Cruz',
            'total_amount' => '180.00',
            'status' => 'completed',
            'payment_status' => 'paid',
            'payment_method' => 'mock_online',
        ]);
        $latestOrder = Order::create([
            'order_number' => '0002',
            'customer_name' => 'Juan Dela Cruz',
            'total_amount' => '50.00',
            'status' => 'accepted',
            'payment_status' => 'unpaid',
            'payment_method' => 'counter',
        ]);

        $this->withSession([
            'customer_name' => 'Juan Dela Cruz',
            'customer_order_ids' => [$olderOrder->id, $latestOrder->id],
        ])
            ->get(route('customer.menu'))
            ->assertOk()
            ->assertSee('Order status')
            ->assertSee(route('customer.order-success', $latestOrder), false)
            ->assertDontSee(route('customer.order-success', $olderOrder), false);
    }

    public function test_customer_is_informed_when_an_order_is_deleted(): void
    {
        $order = \App\Models\Order::create([
            'order_number' => '0001',
            'customer_name' => 'Juan Dela Cruz',
            'total_amount' => '180.00',
            'status' => 'deleted',
            'payment_status' => 'unpaid',
        ]);

        $this->withSession(['customer_name' => 'Juan Dela Cruz', 'customer_order_ids' => [$order->id]])
            ->get(route('customer.order-success', $order))
            ->assertOk()
            ->assertSee('This order has been deleted.');
    }

    private function product(array $attributes = []): Product
    {
        $category = Category::create(['name' => 'Meals', 'is_active' => true]);

        return Product::create(array_merge([
            'category_id' => $category->id,
            'name' => 'Chicken Adobo',
            'price' => '180.00',
            'stock' => 5,
            'is_available' => true,
        ], $attributes));
    }
}
