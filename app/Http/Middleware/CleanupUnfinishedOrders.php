<?php

namespace App\Http\Middleware;

use App\Models\Order;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CleanupUnfinishedOrders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Clean up unfinished orders that are older than 1 hour
        $this->cleanupUnfinishedOrders();

        return $next($request);
    }

    /**
     * Clean up unfinished orders
     */
    private function cleanupUnfinishedOrders(): void
    {
        try {
            // Find orders that are in draft status and older than 1 hour
            $unfinishedOrders = Order::where('status', 'draft')
                ->where('created_at', '<', now()->subHour())
                ->get();

            foreach ($unfinishedOrders as $order) {
                // Delete all order items first
                $order->items()->delete();
                // Delete the order
                $order->delete();
            }

            if ($unfinishedOrders->count() > 0) {
                Log::info("Cleaned up {$unfinishedOrders->count()} unfinished orders");
            }
        } catch (\Exception $e) {
            Log::error('Error cleaning up unfinished orders: ' . $e->getMessage());
        }
    }
}
