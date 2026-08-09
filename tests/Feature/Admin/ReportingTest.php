<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportingTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_uses_completed_orders_for_sales_charts_and_metrics(): void
    {
        $user = User::factory()->admin()->create();
        $product = $this->product(['stock' => 4]);
        $this->order($product, 'completed', 2, now()->subDays(2));
        $this->order($product, 'accepted', 4, now()->subDay());

        $this->actingAs($user)
            ->get(route('admin.dashboard', [
                'start_date' => now()->subDays(3)->toDateString(),
                'end_date' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('Daily Revenue')
            ->assertSee('Top Products')
            ->assertSee('Chicken Adobo')
            ->assertSee('360');
    }

    public function test_reports_can_be_exported_as_pdf_and_excel_for_the_selected_dates(): void
    {
        $user = User::factory()->admin()->create();
        $product = $this->product();
        $this->order($product, 'completed', 1, now()->subDay());
        $dates = ['start_date' => now()->subDays(2)->toDateString(), 'end_date' => now()->toDateString()];

        $this->actingAs($user)
            ->get(route('admin.reports.index', $dates))
            ->assertOk()
            ->assertSee('Orders in Selected Period')
            ->assertSee('#0001');

        $this->actingAs($user)
            ->get(route('admin.reports.pdf', $dates))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($user)
            ->get(route('admin.reports.excel', $dates))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_reports_reject_a_date_range_with_the_end_before_the_start(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->get(route('admin.reports.index', ['start_date' => '2026-08-07', 'end_date' => '2026-08-01']))
            ->assertSessionHasErrors(['start_date', 'end_date']);
    }

    public function test_reports_show_only_orders_inside_the_selected_date_range(): void
    {
        $user = User::factory()->admin()->create();
        $product = $this->product();
        $included = $this->order($product, 'accepted', 1, now()->subDays(2));
        $excluded = $this->order($product, 'completed', 1, now()->subDays(10));

        $this->actingAs($user)
            ->get(route('admin.reports.index', [
                'start_date' => now()->subDays(3)->toDateString(),
                'end_date' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('#'.$included->order_number)
            ->assertDontSee('#'.$excluded->order_number);
    }

    private function product(array $attributes = []): Product
    {
        $category = Category::create(['name' => 'Meals', 'is_active' => true]);

        return Product::create(array_merge([
            'category_id' => $category->id,
            'name' => 'Chicken Adobo',
            'price' => '180.00',
            'stock' => 10,
            'is_available' => true,
        ], $attributes));
    }

    private function order(Product $product, string $status, int $quantity, $createdAt): Order
    {
        $order = Order::create([
            'order_number' => str_pad((string) (Order::count() + 1), 4, '0', STR_PAD_LEFT),
            'customer_name' => 'Juan Dela Cruz',
            'total_amount' => $quantity * 180,
            'status' => $status,
            'payment_status' => 'paid',
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => '180.00',
            'subtotal' => $quantity * 180,
        ]);
        $order->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->save();

        return $order;
    }
}
