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
        $companies = Person::whereNotNull('fiscalapiId')->get();
        $customers = Person::whereNotNull('fiscalapiId')->get();

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
            'issuerId' => 'nullable|exists:people,id',
            'recipientId' => 'nullable|exists:people,id',
        ]);

        $order = Order::create([
            'issuer_id' => $request->issuerId,
            'recipient_id' => $request->recipientId,
            'status' => 'draft',
        ]);

        return response()->json([
            'success' => true,
            'order' => $order,
            'message' => 'Orden creada correctamente'
        ]);
    }

    /**
     * Get order items for a specific order.
     */
    public function getOrderItems($orderId): JsonResponse
    {
        $order = Order::with('items.product')->findOrFail($orderId);

        return response()->json([
            'success' => true,
            'items' => $order->items,
            'message' => 'Items de orden obtenidos correctamente'
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

        // Verificar que el producto tenga ID de FiscalAPI
        if (!$product->fiscalapiId) {
            return response()->json([
                'success' => false,
                'message' => 'El producto no tiene un ID de FiscalAPI válido'
            ], 400);
        }

        $discountPercentage = $request->discountPercentage ?? 0;
        $subtotal = $request->quantity * $product->unitPrice * (1 - $discountPercentage / 100);

        $orderItem = OrderItem::create([
            'order_id' => $request->orderId,
            'product_id' => $request->productId,
            'quantity' => $request->quantity,
            'unit_price' => $product->unitPrice,
            'discount_percentage' => $discountPercentage,
            'subtotal' => $subtotal,
        ]);

        // Recalculate order totals
        $order = Order::with('items.product')->findOrFail($request->orderId);
        $order->calculateTotals();

        return response()->json([
            'success' => true,
            'orderItem' => $orderItem->load('product'),
            'order' => $order->fresh(['items.product']),
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
            'order' => $order->fresh(['items.product']),
            'message' => 'Cantidad actualizada correctamente'
        ]);
    }

    /**
     * Update order item discount.
     */
    public function updateDiscount(Request $request): JsonResponse
    {
        $request->validate([
            'orderItemId' => 'required|exists:order_items,id',
            'discountPercentage' => 'required|numeric|min:0|max:100',
        ]);

        $orderItem = OrderItem::findOrFail($request->orderItemId);
        $orderItem->update(['discount_percentage' => $request->discountPercentage]);
        $orderItem->calculateSubtotal();

        // Recalculate order totals
        $order = $orderItem->order;
        $order->calculateTotals();

        return response()->json([
            'success' => true,
            'orderItem' => $orderItem->fresh(),
            'order' => $order->fresh(['items.product']),
            'message' => 'Descuento actualizado correctamente'
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
            'order' => $order->fresh(['items.product']),
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
            // Verificar que el emisor tenga ID de FiscalAPI
            if ($request->issuerId) {
                $issuer = Person::find($request->issuerId);
                if (!$issuer || !$issuer->fiscalapiId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El emisor seleccionado no tiene un ID de FiscalAPI válido'
                    ], 400);
                }
            }
            $order->update(['issuer_id' => $request->issuerId]);
        }

        if ($request->has('recipientId')) {
            // Verificar que el receptor tenga ID de FiscalAPI
            if ($request->recipientId) {
                $recipient = Person::find($request->recipientId);
                if (!$recipient || !$recipient->fiscalapiId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El receptor seleccionado no tiene un ID de FiscalAPI válido'
                    ], 400);
                }
            }
            $order->update(['recipient_id' => $request->recipientId]);
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

        $order = Order::with(['recipient', 'issuer', 'items.product'])->findOrFail($request->orderId);

        if ($order->items()->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede completar una venta sin productos'
            ], 400);
        }

        // Verificar que tenga emisor y receptor
        if (!$order->issuer || !$order->recipient) {
            return response()->json([
                'success' => false,
                'message' => 'La orden debe tener un emisor y receptor asignados'
            ], 400);
        }

        // Verificar que el emisor tenga ID de FiscalAPI
        if (!$order->issuer->fiscalapiId) {
            return response()->json([
                'success' => false,
                'message' => 'El emisor no tiene un ID de FiscalAPI válido'
            ], 400);
        }

        // Verificar que el receptor tenga ID de FiscalAPI
        if (!$order->recipient->fiscalapiId) {
            return response()->json([
                'success' => false,
                'message' => 'El receptor no tiene un ID de FiscalAPI válido'
            ], 400);
        }

        // Verificar que todos los productos tengan ID de FiscalAPI
        foreach ($order->items as $item) {
            if (!$item->product || !$item->product->fiscalapiId) {
                return response()->json([
                    'success' => false,
                    'message' => 'El producto "' . ($item->product?->description ?? 'N/A') . '" no tiene un ID de FiscalAPI válido'
                ], 400);
            }
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
            ->whereNotNull('fiscalapiId') // Solo productos con ID de FiscalAPI
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }
}
