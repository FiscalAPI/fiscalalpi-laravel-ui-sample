<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\FiscalApiInvoiceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

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
        $query = Order::with(['recipient', 'issuer'])
            ->orderBy('created_at', 'desc');

        // Filtro por defecto: mostrar solo órdenes completadas (facturables)
        $defaultStatus = $request->get('status', 'completed');
        $query->where('status', $defaultStatus);

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

        // Permitir cambiar el estado si se especifica explícitamente
        if ($request->filled('status') && $request->status !== 'completed') {
            $query->where('status', $request->status);
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
    public function generateInvoice(Request $request, Order $order): JsonResponse
    {
        try {
            // Verificar que la orden no tenga factura ya
            if ($order->invoice_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta venta ya tiene una factura generada'
                ], 400);
            }

            // Generar la factura usando FiscalAPI
            $result = $this->fiscalApiService->generateInvoice($order);

            if ($result['success']) {
                // Actualizar la orden con el ID de la factura
                $order->update([
                    'invoice_id' => $result['invoice_id'],
                    'status' => 'completed'
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Factura generada exitosamente',
                    'invoice_id' => $result['invoice_id']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get PDF for an invoice
     */
    public function getInvoicePdf(string $invoiceId): JsonResponse
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
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el PDF: ' . $e->getMessage()
            ], 500);
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
