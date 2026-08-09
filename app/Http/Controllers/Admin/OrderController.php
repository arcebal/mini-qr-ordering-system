<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    private const TRANSITIONS = [
        'accepted' => ['preparing'],
        'preparing' => ['completed'],
        'completed' => [],
    ];

    public function index(): View
    {
        $orders = Order::query()
            ->where('status', '!=', 'deleted')
            ->withCount('items')
            ->latest()
            ->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load('items.product');

        return view('admin.orders.show', [
            'order' => $order,
            'allowedStatuses' => self::TRANSITIONS[$order->status] ?? [],
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:accepted,preparing,completed',
        ]);

        $status = $request->string('status')->toString();

        if (! in_array($status, self::TRANSITIONS[$order->status] ?? [], true)) {
            return back()->with('error', 'That status change is not allowed for this order.');
        }

        if ($status === 'completed' && $order->payment_status !== 'paid') {
            return back()->with('error', 'Payment must be marked as paid before completing this order.');
        }

        $order->update(['status' => $status]);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Order #'.$order->order_number.' marked as '.str($status)->headline().'.');
    }

    public function updatePayment(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'payment_status' => 'required|in:paid',
        ]);

        if ($order->status === 'deleted') {
            return back()->with('error', 'Deleted orders cannot have their payment updated.');
        }

        $order->update(['payment_status' => 'paid']);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Order #'.$order->order_number.' payment marked as paid.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $orderNumber = $order->order_number;
        $order->update(['status' => 'deleted']);

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Order #'.$orderNumber.' was marked as deleted.');
    }
}
