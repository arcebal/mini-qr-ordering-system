<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_order_list_and_details(): void
    {
        $user = User::factory()->admin()->create();
        $order = $this->order();

        $this->actingAs($user)
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee('#0001')
            ->assertSee('Juan Dela Cruz');

        $this->actingAs($user)
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee('Chicken Adobo')
            ->assertSee('Payment')
            ->assertSee('Paid')
            ->assertSee('Mark as Preparing');
    }

    public function test_order_can_follow_its_allowed_status_lifecycle(): void
    {
        $user = User::factory()->admin()->create();
        $order = $this->order();

        $this->actingAs($user)
            ->patch(route('admin.orders.update', $order), ['status' => 'preparing'])
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'preparing']);

        $this->actingAs($user)
            ->patch(route('admin.orders.update', $order), ['status' => 'completed'])
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'completed']);
    }

    public function test_admin_can_mark_an_unpaid_order_as_paid(): void
    {
        $user = User::factory()->admin()->create();
        $order = $this->order(['payment_status' => 'unpaid']);

        $this->actingAs($user)
            ->patch(route('admin.orders.payment.update', $order), ['payment_status' => 'paid'])
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'payment_status' => 'paid']);
    }

    public function test_unpaid_order_cannot_be_completed(): void
    {
        $user = User::factory()->admin()->create();
        $order = $this->order(['status' => 'preparing', 'payment_status' => 'unpaid']);

        $this->actingAs($user)
            ->from(route('admin.orders.show', $order))
            ->patch(route('admin.orders.update', $order), ['status' => 'completed'])
            ->assertRedirect(route('admin.orders.show', $order))
            ->assertSessionHas('error', 'Payment must be marked as paid before completing this order.');

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'preparing']);
    }

    public function test_order_cannot_skip_a_status_transition(): void
    {
        $user = User::factory()->admin()->create();
        $order = $this->order();

        $this->actingAs($user)
            ->from(route('admin.orders.show', $order))
            ->patch(route('admin.orders.update', $order), ['status' => 'completed'])
            ->assertRedirect(route('admin.orders.show', $order))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'accepted']);
    }

    public function test_authenticated_user_can_delete_an_order(): void
    {
        $user = User::factory()->admin()->create();
        $order = $this->order();

        $this->actingAs($user)
            ->delete(route('admin.orders.destroy', $order))
            ->assertRedirect(route('admin.orders.index'));

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'deleted']);
        $this->assertDatabaseCount('order_items', 1);
    }

    private function order(array $attributes = []): Order
    {
        $category = Category::create(['name' => 'Meals', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Chicken Adobo',
            'price' => '180.00',
            'stock' => 5,
            'is_available' => true,
        ]);
        $order = Order::create(array_merge([
            'order_number' => '0001',
            'customer_name' => 'Juan Dela Cruz',
            'total_amount' => '360.00',
            'status' => 'accepted',
            'payment_status' => 'paid',
            'payment_method' => 'counter',
        ], $attributes));
        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => '180.00',
            'subtotal' => '360.00',
        ]);

        return $order;
    }
}
