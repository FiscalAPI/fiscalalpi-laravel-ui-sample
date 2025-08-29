<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Person;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PosController extends Controller
{
        /**
     * Display the POS interface.
     */
    public function index()
    {
        $companies = Person::all();
        $customers = Person::all();

        // Create a new order automatically when POS opens
        $order = Order::create([
            'issuer_id' => null,
            'recipient_id' => null,
            'status' => 'draft',
        ]);

        return view('components.pos.index', compact('companies', 'customers', 'order'));
    }

    /**
     * Create a new order.
     */
    public function createOrder(Request $request): JsonResponse
    {
        $request->validate([
            'issuerId' => 'required|exists:people,id',
            'recipientId' => 'required|exists:people,id',
        ]);

        $order = Order::create([
            'issuerId' => $request->issuerId,
            'recipientId' => $request->recipientId,
            'status' => 'draft',
        ]);

        return response()->json([
            'success' => true,
            'order' => $order,
            'message' => 'Orden creada correctamente'
        ]);
    }

    /**
     * Add a product to the order.
     */
    public function addProduct(Request $request): JsonResponse
    {
        $request->validate([
            'orderId' => 'required|exists:orders,id',
            'productId' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'discountPercentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $product = Product::findOrFail($request->productId);

        $orderItem = OrderItem::create([
            'order_id' => $request->orderId,
            'product_id' => $request->productId,
            'quantity' => $request->quantity,
            'unit_price' => $product->unitPrice,
            'discount_percentage' => $request->discountPercentage ?? 0,
            'subtotal' => $request->quantity * $product->unitPrice * (1 - ($request->discountPercentage ?? 0) / 100),
        ]);

        // Recalculate order totals
        $order = Order::findOrFail($request->orderId);
        $order->calculateTotals();

        return response()->json([
            'success' => true,
            'orderItem' => $orderItem->load('product'),
            'order' => $order->fresh(),
            'message' => 'Producto agregado correctamente'
        ]);
    }

    /**
     * Update order item quantity.
     */
    public function updateQuantity(Request $request): JsonResponse
    {
        $request->validate([
            'orderItemId' => 'required|exists:order_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $orderItem = OrderItem::findOrFail($request->orderItemId);
        $orderItem->update(['quantity' => $request->quantity]);
        $orderItem->calculateSubtotal();

        // Recalculate order totals
        $order = $orderItem->order;
        $order->calculateTotals();

        return response()->json([
            'success' => true,
            'orderItem' => $orderItem->fresh(),
            'order' => $order->fresh(),
            'message' => 'Cantidad actualizada correctamente'
        ]);
    }

    /**
     * Remove a product from the order.
     */
    public function removeProduct(Request $request): JsonResponse
    {
        $request->validate([
            'orderItemId' => 'required|exists:order_items,id',
        ]);

        $orderItem = OrderItem::findOrFail($request->orderItemId);
        $order = $orderItem->order;

        $orderItem->delete();
        $order->calculateTotals();

        return response()->json([
            'success' => true,
            'order' => $order->fresh(),
            'message' => 'Producto removido correctamente'
        ]);
    }

    /**
     * Update order customer or company.
     */
    public function updateOrder(Request $request): JsonResponse
    {
        $request->validate([
            'orderId' => 'required|exists:orders,id',
            'issuerId' => 'nullable|exists:people,id',
            'recipientId' => 'nullable|exists:people,id',
        ]);

        $order = Order::findOrFail($request->orderId);

        if ($request->has('issuerId')) {
            $order->update(['issuerId' => $request->issuerId]);
        }

        if ($request->has('recipientId')) {
            $order->update(['recipientId' => $request->recipientId]);
        }

        return response()->json([
            'success' => true,
            'order' => $order->fresh(),
            'message' => 'Orden actualizada correctamente'
        ]);
    }

    /**
     * Cancel the current sale.
     */
    public function cancelSale(Request $request): JsonResponse
    {
        $request->validate([
            'orderId' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->orderId);
        $order->update(['status' => 'cancelled']);

        // Delete all order items
        $order->items()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Venta cancelada correctamente'
        ]);
    }

    /**
     * Complete the sale.
     */
    public function endSale(Request $request): JsonResponse
    {
        $request->validate([
            'orderId' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->orderId);

        if ($order->items()->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede completar una venta sin productos'
            ], 400);
        }

        $order->update(['status' => 'completed']);

        return response()->json([
            'success' => true,
            'order' => $order->fresh(),
            'message' => 'Venta completada correctamente'
        ]);
    }

    /**
     * Search products.
     */
    public function searchProducts(Request $request): JsonResponse
    {
        $query = $request->get('query', '');

        $products = Product::where('description', 'like', "%{$query}%")
            ->orWhere('id', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }
}
