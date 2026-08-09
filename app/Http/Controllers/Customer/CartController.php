<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $items = $this->items();

        return view('customer.cart.index', [
            'items' => $items,
            'subtotal' => $items->sum('subtotal'),
        ]);
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        if (! $this->isOrderable($product)) {
            return back()->with('error', 'This product is currently unavailable.');
        }

        $cart = session('customer_cart', []);
        $quantity = (int) ($cart[$product->id] ?? 0);

        if ($quantity >= $product->stock) {
            return back()->with('warning', 'The maximum available quantity is already in your cart.');
        }

        $cart[$product->id] = $quantity + 1;
        session(['customer_cart' => $cart]);

        return back()->with('success', $product->name.' added to your cart.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'operation' => 'nullable|in:increase,decrease',
            'quantity' => 'nullable|integer|min:0',
        ]);

        $cart = session('customer_cart', []);

        if (! isset($cart[$product->id])) {
            return redirect()->route('customer.cart.index');
        }

        $quantity = (int) $cart[$product->id];

        if ($request->input('operation') === 'increase') {
            if (! $this->isOrderable($product) || $quantity >= $product->stock) {
                return back()->with('warning', 'No more of this product is currently available.');
            }

            $quantity++;
        } elseif ($request->input('operation') === 'decrease') {
            $quantity--;
        } else {
            $quantity = (int) $request->input('quantity', $quantity);
        }

        if ($quantity <= 0) {
            unset($cart[$product->id]);
        } else {
            $cart[$product->id] = min($quantity, $product->stock);
        }

        session(['customer_cart' => $cart]);

        return back();
    }

    public function remove(Product $product): RedirectResponse
    {
        $cart = session('customer_cart', []);
        unset($cart[$product->id]);
        session(['customer_cart' => $cart]);

        return back()->with('success', 'Item removed from your cart.');
    }

    public function checkout(): View|RedirectResponse
    {
        $items = $this->items();

        if ($items->isEmpty()) {
            return redirect()->route('customer.menu')->with('warning', 'Your cart is empty.');
        }

        return view('customer.checkout', [
            'items' => $items,
            'subtotal' => $items->sum('subtotal'),
        ]);
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $cart = session('customer_cart', []);
        $customerName = $request->session()->get('customer_name');

        if (empty($cart) || ! $customerName) {
            return redirect()->route('customer.menu')->with('warning', 'Your cart is empty.');
        }

        $paymentMethod = $request->validate([
            'payment_method' => 'nullable|in:counter,mock_online',
        ])['payment_method'] ?? 'counter';

        $order = DB::transaction(function () use ($cart, $customerName, $paymentMethod) {
            $products = Product::query()
                ->whereIn('id', array_keys($cart))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $total = 0;
            $lineItems = [];

            foreach ($cart as $productId => $quantity) {
                $product = $products->get($productId);

                if (! $product || ! $this->isOrderable($product) || $quantity > $product->stock) {
                    throw ValidationException::withMessages([
                        'cart' => 'One or more items in your cart are no longer available in the requested quantity.',
                    ]);
                }

                $unitPrice = (float) $product->price;
                $subtotal = $unitPrice * $quantity;
                $total += $subtotal;
                $lineItems[] = compact('product', 'quantity', 'unitPrice', 'subtotal');
            }

            $order = Order::create([
                'order_number' => str_pad((string) ((Order::query()->max('id') ?? 0) + 1), 4, '0', STR_PAD_LEFT),
                'customer_name' => $customerName,
                'total_amount' => $total,
                'status' => 'accepted',
                'payment_status' => $paymentMethod === 'mock_online' ? 'paid' : 'unpaid',
                'payment_method' => $paymentMethod,
            ]);

            foreach ($lineItems as $lineItem) {
                $order->items()->create([
                    'product_id' => $lineItem['product']->id,
                    'quantity' => $lineItem['quantity'],
                    'unit_price' => $lineItem['unitPrice'],
                    'subtotal' => $lineItem['subtotal'],
                ]);

                $remainingStock = $lineItem['product']->stock - $lineItem['quantity'];

                $lineItem['product']->update([
                    'stock' => $remainingStock,
                    'is_available' => $remainingStock > 0,
                ]);
            }

            return $order;
        });

        $request->session()->forget('customer_cart');
        $request->session()->push('customer_order_ids', $order->id);

        return redirect()->route('customer.order-success', $order);
    }

    public function success(Order $order): View
    {
        $this->ensureOrderBelongsToSession($order);

        return view('customer.success', compact('order'));
    }

    public function status(Request $request, Order $order): \Illuminate\Http\JsonResponse
    {
        $this->ensureOrderBelongsToSession($order);

        return response()->json([
            'status' => $order->status,
            'label' => str($order->status)->headline()->toString(),
            'payment_method' => $order->payment_method,
            'payment_method_label' => $order->payment_method === 'mock_online' ? 'Mock online payment' : 'Pay at counter',
            'payment_status' => $order->payment_status,
            'payment_status_label' => str($order->payment_status)->headline()->toString(),
            'updated_at' => $order->updated_at->toIso8601String(),
        ]);
    }

    private function items(): Collection
    {
        $cart = session('customer_cart', []);

        if (empty($cart)) {
            return collect();
        }

        return Product::query()
            ->with('category')
            ->whereIn('id', array_keys($cart))
            ->get()
            ->map(function (Product $product) use ($cart) {
                $quantity = (int) $cart[$product->id];

                return (object) [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => (float) $product->price * $quantity,
                ];
            });
    }

    private function isOrderable(Product $product): bool
    {
        return $product->is_available && $product->stock > 0;
    }

    private function ensureOrderBelongsToSession(Order $order): void
    {
        abort_unless(in_array($order->id, session('customer_order_ids', []), true), 403);
    }
}
