<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\FiscalApiInvoiceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class SalesController extends Controller
{
    protected FiscalApiInvoiceService $fiscalApiService;

    public function __construct(FiscalApiInvoiceService $fiscalApiService)
    {
        $this->fiscalApiService = $fiscalApiService;
    }

    /**
     * Display the sales index page
     */
    public function index(Request $request): View
    {
        $query = Order::with(['recipient', 'issuer', 'items.product'])
            ->orderBy('created_at', 'desc');

        // Aplicar filtro por status
        $status = $request->get('status', Order::STATUS_COMPLETED);
        $query->where('status', $status);

        // Aplicar filtros adicionales
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('recipient', function ($q) use ($search) {
                      $q->where('legalName', 'like', "%{$search}%")
                        ->orWhere('tin', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_range')) {
            $query->whereBetween('created_at', $this->getDateRange($request->date_range));
        }

        $orders = $query->paginate(15);

        return view('sales.index', compact('orders'));
    }

    /**
     * Generate invoice for an order
     */
    public function generateInvoice(Request $request, $id): JsonResponse
    {
        try {
            // Buscar la orden por ID con todas las relaciones necesarias
            $order = Order::with(['recipient', 'issuer', 'items.product'])->findOrFail($id);

            // Verificar que la orden no tenga factura ya
            if ($order->invoice_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta venta ya tiene una factura generada'
                ], 400);
            }

            // Verificar que la orden esté completada
            if ($order->status !== Order::STATUS_COMPLETED) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden facturar órdenes completadas'
                ], 400);
            }

            // Verificar que la orden tenga items
            if ($order->items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'La orden no tiene productos para facturar'
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

            Log::info('Starting invoice generation', [
                'order_id' => $order->id,
                'issuer_id' => $order->issuer->id,
                'recipient_id' => $order->recipient->id,
                'items_count' => $order->items->count()
            ]);

            // Generar la factura usando FiscalAPI
            $result = $this->fiscalApiService->generateInvoice($order);

            if ($result['success']) {
                // Actualizar la orden con el ID de la factura y cambiar status a 'invoiced'
                $order->update([
                    'invoice_id' => $result['invoice_id'],
                    'status' => Order::STATUS_INVOICED
                ]);

                Log::info('Invoice generated successfully', [
                    'order_id' => $order->id,
                    'invoice_id' => $result['invoice_id'],
                    'invoice_uuid' => $result['invoice_uuid'] ?? null,
                    'invoice_number' => $result['invoice_number'] ?? null,
                    'new_status' => Order::STATUS_INVOICED
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Factura generada exitosamente',
                    'invoice_id' => $result['invoice_id'],
                    'invoice_uuid' => $result['invoice_uuid'] ?? null,
                    'invoice_number' => $result['invoice_number'] ?? null
                ]);
            } else {
                Log::error('Failed to generate invoice', [
                    'order_id' => $order->id,
                    'error' => $result['message']
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Exception while generating invoice', [
                'order_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get PDF for an invoice (serves PDF directly)
     */
    public function getInvoicePdf(string $invoiceId)
    {
        try {
            // Verificar que la factura existe
            if (!$this->fiscalApiService->validateInvoice($invoiceId)) {
                abort(400, 'ID de factura inválido');
            }

            // Obtener el PDF desde FiscalAPI
            $pdfRequest = ['invoiceId' => $invoiceId];
            $apiResponse = $this->fiscalApiService->getFiscalApiClient()->getInvoiceService()->getPdf($pdfRequest);
            $responseData = $apiResponse->getJson();

            if (!$responseData['succeeded']) {
                Log::error('Failed to get PDF from FiscalAPI', [
                    'invoice_id' => $invoiceId,
                    'response' => $responseData
                ]);
                abort(404, 'No se pudo obtener el PDF de la factura');
            }

            // Convertir base64 a archivo y servir
            $pdfContent = base64_decode($responseData['data']['base64File']);
            $fileName = $responseData['data']['fileName'] ?? 'invoice.pdf';

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . $fileName . '"');

        } catch (\Exception $e) {
            Log::error('Exception while getting PDF', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage()
            ]);

            abort(500, 'Error al obtener el PDF');
        }
    }

    /**
     * Get PDF info for an invoice (returns JSON with PDF URL)
     */
    public function getInvoicePdfInfo(string $invoiceId): JsonResponse
    {
        try {
            // Verificar que la factura existe
            if (!$this->fiscalApiService->validateInvoice($invoiceId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID de factura inválido'
                ], 400);
            }

            // Obtener la URL del PDF
            $pdfUrl = $this->fiscalApiService->getInvoicePdfUrl($invoiceId);

            if (!$pdfUrl) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo obtener el PDF de la factura'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'pdf_url' => $pdfUrl
            ]);
        } catch (\Exception $e) {
            Log::error('Exception while getting PDF info', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get XML for an invoice
     */
    public function getInvoiceXml(string $invoiceId): JsonResponse
    {
        try {
            // Verificar que la factura existe
            if (!$this->fiscalApiService->validateInvoice($invoiceId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID de factura inválido'
                ], 400);
            }

            // Obtener el XML de la factura
            $xmlData = $this->fiscalApiService->getInvoiceXml($invoiceId);

            if (!$xmlData) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo obtener el XML de la factura'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'xml_data' => $xmlData
            ]);
        } catch (\Exception $e) {
            Log::error('Exception while getting XML', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el XML: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send invoice by email
     */
    public function sendInvoiceByEmail(Request $request, string $invoiceId): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email'
            ]);

            // Verificar que la factura existe
            if (!$this->fiscalApiService->validateInvoice($invoiceId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID de factura inválido'
                ], 400);
            }

            // Enviar la factura por correo
            $result = $this->fiscalApiService->sendInvoiceByEmail($invoiceId, $request->email);

            if ($result['success']) {
                Log::info('Invoice sent by email successfully', [
                    'invoice_id' => $invoiceId,
                    'email' => $request->email
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ]);
            } else {
                Log::error('Failed to send invoice by email', [
                    'invoice_id' => $invoiceId,
                    'email' => $request->email,
                    'error' => $result['message']
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Exception while sending invoice by email', [
                'invoice_id' => $invoiceId,
                'email' => $request->email ?? 'not provided',
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download invoice PDF
     */
    public function downloadInvoicePdf(string $invoiceId)
    {
        try {
            // Verificar que la factura existe
            if (!$this->fiscalApiService->validateInvoice($invoiceId)) {
                abort(400, 'ID de factura inválido');
            }

            // Obtener el XML de la factura (que incluye el PDF en base64)
            $xmlData = $this->fiscalApiService->getInvoiceXml($invoiceId);

            if (!$xmlData) {
                abort(404, 'No se pudo obtener el PDF de la factura');
            }

            // Convertir base64 a archivo y descargar
            $pdfContent = base64_decode($xmlData['base64File']);
            $fileName = $xmlData['fileName'] ?? 'invoice.pdf';

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        } catch (\Exception $e) {
            Log::error('Exception while downloading PDF', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage()
            ]);

            abort(500, 'Error al descargar el PDF');
        }
    }

    /**
     * Get date range for filtering
     */
    private function getDateRange(string $range): array
    {
        $now = now();

        return match ($range) {
            'today' => [$now->startOfDay(), $now->endOfDay()],
            'week' => [$now->startOfWeek(), $now->endOfWeek()],
            'month' => [$now->startOfMonth(), $now->endOfMonth()],
            'quarter' => [$now->startOfQuarter(), $now->endOfQuarter()],
            'year' => [$now->startOfYear(), $now->endOfYear()],
            default => [$now->subYear(), $now]
        };
    }
}
