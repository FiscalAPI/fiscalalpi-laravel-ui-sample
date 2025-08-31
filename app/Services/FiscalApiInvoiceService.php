<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Person;
use App\Models\Product;
use Fiscalapi\Services\FiscalApiClient;
use Illuminate\Support\Facades\Log;
use Exception;

class FiscalApiInvoiceService
{
    protected $fiscalApi;

    public function __construct(FiscalApiClient $fiscalApi)
    {
        $this->fiscalApi = $fiscalApi;
    }

    /**
     * Generate invoice for an order using FiscalAPI
     */
    public function generateInvoice(Order $order): array
    {
        try {
            // Validar que la orden tenga los datos necesarios
            if (!$this->validateOrderForInvoice($order)) {
                return [
                    'success' => false,
                    'message' => 'La orden no tiene todos los datos necesarios para generar la factura'
                ];
            }

            // Preparar datos para FiscalAPI
            $invoiceData = $this->prepareInvoiceData($order);

            Log::info('Sending invoice data to FiscalAPI', [
                'order_id' => $order->id,
                'invoice_data' => $invoiceData
            ]);

            // Crear factura en FiscalAPI
            $apiResponse = $this->fiscalApi->getInvoiceService()->create($invoiceData);
            $responseData = $apiResponse->getJson();

            Log::info('FiscalAPI response', [
                'order_id' => $order->id,
                'response' => $responseData
            ]);

            if (!$responseData['succeeded']) {
                Log::error('Failed to create invoice in FiscalAPI', [
                    'order_id' => $order->id,
                    'response' => $responseData
                ]);

                return [
                    'success' => false,
                    'message' => 'Error al crear la factura en FiscalAPI: ' . ($responseData['message'] ?? 'Error desconocido')
                ];
            }

            // Extraer información de la respuesta
            $invoiceId = $responseData['data']['id'];
            $invoiceUuid = $responseData['data']['uuid'] ?? null;
            $invoiceNumber = $responseData['data']['number'] ?? null;

            Log::info('Invoice created successfully in FiscalAPI', [
                'order_id' => $order->id,
                'invoice_id' => $invoiceId,
                'invoice_uuid' => $invoiceUuid,
                'invoice_number' => $invoiceNumber
            ]);

            return [
                'success' => true,
                'invoice_id' => $invoiceId,
                'invoice_uuid' => $invoiceUuid,
                'invoice_number' => $invoiceNumber,
                'message' => 'Factura generada exitosamente'
            ];

        } catch (Exception $e) {
            Log::error('Exception while generating invoice', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Error interno al generar la factura: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get PDF URL for an invoice
     */
    public function getInvoicePdfUrl(string $invoiceId): ?string
    {
        try {
            if (empty($invoiceId)) {
                return null;
            }

            // Obtener PDF desde FiscalAPI
            $pdfRequest = ['invoiceId' => $invoiceId];
            $apiResponse = $this->fiscalApi->getInvoiceService()->getPdf($pdfRequest);
            $responseData = $apiResponse->getJson();

            if (!$responseData['succeeded']) {
                Log::error('Failed to get PDF from FiscalAPI', [
                    'invoice_id' => $invoiceId,
                    'response' => $responseData
                ]);
                return null;
            }

            // Convertir base64 a archivo temporal y retornar URL
            $base64File = $responseData['data']['base64File'];
            $fileName = $responseData['data']['fileName'] ?? 'invoice.pdf';

            // Crear archivo temporal
            $tempPath = storage_path('app/temp/' . $fileName);
            $tempDir = dirname($tempPath);

            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            file_put_contents($tempPath, base64_decode($base64File));

            // Retornar URL temporal (en producción, esto debería ser una ruta real)
            return route('sales.invoice-pdf', ['invoiceId' => $invoiceId]);

        } catch (Exception $e) {
            Log::error('Exception while getting PDF', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get XML for an invoice
     */
    public function getInvoiceXml(string $invoiceId): ?array
    {
        try {
            if (empty($invoiceId)) {
                return null;
            }

            // Obtener XML desde FiscalAPI
            $apiResponse = $this->fiscalApi->getInvoiceService()->getXml($invoiceId);
            $responseData = $apiResponse->getJson();

            if (!$responseData['succeeded']) {
                Log::error('Failed to get XML from FiscalAPI', [
                    'invoice_id' => $invoiceId,
                    'response' => $responseData
                ]);
                return null;
            }

            return [
                'base64File' => $responseData['data']['base64File'],
                'fileName' => $responseData['data']['fileName'],
                'fileExtension' => $responseData['data']['fileExtension']
            ];

        } catch (Exception $e) {
            Log::error('Exception while getting XML', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Send invoice by email
     */
    public function sendInvoiceByEmail(string $invoiceId, string $email): array
    {
        try {
            if (empty($invoiceId) || empty($email)) {
                return [
                    'success' => false,
                    'message' => 'ID de factura y email son requeridos'
                ];
            }

            // Enviar factura por correo usando FiscalAPI
            $emailRequest = [
                'invoiceId' => $invoiceId,
                'toEmail' => $email
            ];

            $apiResponse = $this->fiscalApi->getInvoiceService()->send($emailRequest);
            $responseData = $apiResponse->getJson();

            if (!$responseData['succeeded']) {
                Log::error('Failed to send invoice by email', [
                    'invoice_id' => $invoiceId,
                    'email' => $email,
                    'response' => $responseData
                ]);

                return [
                    'success' => false,
                    'message' => 'Error al enviar la factura por correo: ' . ($responseData['message'] ?? 'Error desconocido')
                ];
            }

            Log::info('Invoice sent by email successfully', [
                'invoice_id' => $invoiceId,
                'email' => $email
            ]);

            return [
                'success' => true,
                'message' => 'Factura enviada por correo exitosamente'
            ];

        } catch (Exception $e) {
            Log::error('Exception while sending invoice by email', [
                'invoice_id' => $invoiceId,
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error interno al enviar la factura: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check if invoice exists and is valid
     */
    public function validateInvoice(string $invoiceId): bool
    {
        try {
            if (empty($invoiceId)) {
                return false;
            }

            // Validar formato básico del ID
            if (!preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $invoiceId)) {
                return false;
            }

            // Aquí podrías hacer una llamada a FiscalAPI para validar que la factura existe
            // Por ahora, solo validamos el formato
            return true;

        } catch (Exception $e) {
            Log::error('Exception while validating invoice', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Validate order has all required data for invoice generation
     */
    protected function validateOrderForInvoice(Order $order): bool
    {
        // Verificar que la orden tenga items
        if ($order->items->isEmpty()) {
            Log::warning('Order has no items', ['order_id' => $order->id]);
            return false;
        }

        // Verificar que tenga emisor y receptor
        if (!$order->issuer || !$order->recipient) {
            Log::warning('Order missing issuer or recipient', [
                'order_id' => $order->id,
                'has_issuer' => (bool)$order->issuer,
                'has_recipient' => (bool)$order->recipient
            ]);
            return false;
        }

        // Verificar que el emisor tenga ID de FiscalAPI
        if (!$order->issuer->fiscalapiId) {
            Log::warning('Issuer missing FiscalAPI ID', [
                'order_id' => $order->id,
                'issuer_id' => $order->issuer->id,
                'fiscalapi_id' => $order->issuer->fiscalapiId
            ]);
            return false;
        }

        // Verificar que el receptor tenga ID de FiscalAPI
        if (!$order->recipient->fiscalapiId) {
            Log::warning('Recipient missing FiscalAPI ID', [
                'order_id' => $order->id,
                'recipient_id' => $order->recipient->id,
                'fiscalapi_id' => $order->recipient->fiscalapiId
            ]);
            return false;
        }

        // Verificar que todos los productos tengan ID de FiscalAPI
        foreach ($order->items as $item) {
            if (!$item->product || !$item->product->fiscalapiId) {
                Log::warning('Product missing FiscalAPI ID', [
                    'order_id' => $order->id,
                    'item_id' => $item->id,
                    'product_id' => $item->product?->id,
                    'fiscalapi_id' => $item->product?->fiscalapiId
                ]);
                return false;
            }
        }

        return true;
    }

    /**
     * Prepare invoice data for FiscalAPI
     */
    protected function prepareInvoiceData(Order $order): array
    {
        $currentDate = $this->getCurrentDate();
        $invoiceData = [
            'versionCode' => "4.0",
            'series' => "F",
            'date' => $currentDate,
            'paymentFormCode' => "01",
            'currencyCode' => "MXN",
            'typeCode' => "I",
            'expeditionZipCode' => $order->issuer->zipCode,
            'paymentMethodCode' => "PUE",
            'exchangeRate' => 1,
            'exportCode' => "01",
            'issuer' => [
                'id' => $order->issuer->fiscalapiId
            ],
            'recipient' => [
                'id' => $order->recipient->fiscalapiId
            ],
            'items' => []
        ];

        // Preparar items de la factura
        foreach ($order->items as $item) {
            $itemData = [
                'id' => $item->product->fiscalapiId,
                'quantity' => $item->quantity,
            ];

            $invoiceData['items'][] = $itemData;
        }

        Log::info('Prepared invoice data', [
            'order_id' => $order->id,
            'invoice_data' => $invoiceData
        ]);

        return $invoiceData;
    }

    /**
     * Get current date in FiscalAPI format
     */
    protected function getCurrentDate(): string
    {
       // México Central se mantiene en UTC-6 permanentemente desde la eliminación del horario de verano
            date_default_timezone_set('Etc/GMT+6');  // Nota: GMT+6 es UTC-6

            // Formatear la fecha y hora en formato SAT: AAAA-MM-DDThh:mm:ss
            return date('Y-m-d\TH:i:s');
    }


}
