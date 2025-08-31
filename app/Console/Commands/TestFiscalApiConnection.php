<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\FiscalApiInvoiceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestFiscalApiConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fiscalapi:test {order_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test FiscalAPI connection and invoice generation';

    /**
     * Execute the console command.
     */
    public function handle(FiscalApiInvoiceService $fiscalApiService)
    {
        $orderId = $this->argument('order_id');

        if ($orderId) {
            $this->testSpecificOrder($orderId, $fiscalApiService);
        } else {
            $this->testConnection($fiscalApiService);
        }
    }

    private function testConnection(FiscalApiInvoiceService $fiscalApiService)
    {
        $this->info('Testing FiscalAPI connection...');

        try {
            // Buscar una orden completada para probar
            $order = Order::with(['recipient', 'issuer', 'items.product'])
                ->where('status', 'completed')
                ->whereNull('invoice_id')
                ->first();

            if (!$order) {
                $this->error('No hay órdenes completadas sin factura para probar');
                return;
            }

            $this->info("Testing with order ID: {$order->id}");
            $this->info("Issuer: " . ($order->issuer?->legalName ?? 'N/A'));
            $this->info("Recipient: " . ($order->recipient?->legalName ?? 'N/A'));
            $this->info("Items count: " . $order->items->count());

            // Verificar que la orden pueda ser facturada
            if (!$order->canBeInvoiced()) {
                $this->error('La orden no puede ser facturada');
                $this->info('Status: ' . $order->status);
                $this->info('Has invoice: ' . ($order->invoice_id ? 'Yes' : 'No'));
                $this->info('Has items: ' . ($order->items->isNotEmpty() ? 'Yes' : 'No'));
                $this->info('Has issuer: ' . ($order->issuer ? 'Yes' : 'No'));
                $this->info('Has recipient: ' . ($order->recipient ? 'Yes' : 'No'));
                $this->info('Issuer FiscalAPI ID: ' . ($order->issuer?->fiscalapiId ?? 'N/A'));
                $this->info('Recipient FiscalAPI ID: ' . ($order->recipient?->fiscalapiId ?? 'N/A'));
                $this->info('All products have FiscalAPI ID: ' . ($order->allProductsHaveFiscalApiId() ? 'Yes' : 'No'));
                return;
            }

            $this->info('Order validation passed. Testing invoice generation...');

            // Intentar generar la factura
            $result = $fiscalApiService->generateInvoice($order);

            if ($result['success']) {
                $this->info('✅ Invoice generated successfully!');
                $this->info('Invoice ID: ' . $result['invoice_id']);
                $this->info('Invoice UUID: ' . ($result['invoice_uuid'] ?? 'N/A'));
                $this->info('Invoice Number: ' . ($result['invoice_number'] ?? 'N/A'));
            } else {
                $this->error('❌ Failed to generate invoice: ' . $result['message']);
            }

        } catch (\Exception $e) {
            $this->error('Exception occurred: ' . $e->getMessage());
            Log::error('FiscalAPI test failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function testSpecificOrder($orderId, FiscalApiInvoiceService $fiscalApiService)
    {
        $this->info("Testing specific order ID: {$orderId}");

        try {
            $order = Order::with(['recipient', 'issuer', 'items.product'])->find($orderId);

            if (!$order) {
                $this->error("Order {$orderId} not found");
                return;
            }

            $this->info("Order found:");
            $this->info("Status: {$order->status}");
            $this->info("Issuer: " . ($order->issuer?->legalName ?? 'N/A'));
            $this->info("Recipient: " . ($order->recipient?->legalName ?? 'N/A'));
            $this->info("Items count: " . $order->items->count());
            $this->info("Invoice ID: " . ($order->invoice_id ?? 'None'));

            if ($order->canBeInvoiced()) {
                $this->info('✅ Order can be invoiced');
            } else {
                $this->error('❌ Order cannot be invoiced');
                $this->info('Status: ' . $order->status);
                $this->info('Has invoice: ' . ($order->invoice_id ? 'Yes' : 'No'));
                $this->info('Has items: ' . ($order->items->isNotEmpty() ? 'Yes' : 'No'));
                $this->info('Has issuer: ' . ($order->issuer ? 'Yes' : 'No'));
                $this->info('Has recipient: ' . ($order->recipient ? 'Yes' : 'No'));
                $this->info('Issuer FiscalAPI ID: ' . ($order->issuer?->fiscalapiId ?? 'N/A'));
                $this->info('Recipient FiscalAPI ID: ' . ($order->recipient?->fiscalapiId ?? 'N/A'));
                $this->info('All products have FiscalAPI ID: ' . ($order->allProductsHaveFiscalApiId() ? 'Yes' : 'No'));
            }

        } catch (\Exception $e) {
            $this->error('Exception occurred: ' . $e->getMessage());
        }
    }
}
