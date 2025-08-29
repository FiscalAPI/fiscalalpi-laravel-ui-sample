<?php

namespace App\Services;

use App\Models\Product;
use Fiscalapi\Services\FiscalApiClient;
use Illuminate\Support\Facades\Log;
use Exception;

class FiscalApiProductService
{
    protected $fiscalApi;

    public function __construct(FiscalApiClient $fiscalApi)
    {
        $this->fiscalApi = $fiscalApi;
    }

    /**
     * Create product in both local database and FiscalAPI
     */
    public function createProduct(array $data): Product
    {
        try {
            // Create product in FiscalAPI first
            $fiscalApiData = $this->prepareFiscalApiData($data);
            $apiResponse = $this->fiscalApi->getProductService()->create($fiscalApiData);
            $responseData = $apiResponse->getJson();

            if (!$responseData['succeeded']) {
                throw new Exception('Failed to create product in FiscalAPI: ' . ($responseData['message'] ?? 'Unknown error'));
            }

            // Store FiscalAPI ID and create in local database
            $data['fiscalapiId'] = $responseData['data']['id'];
            $product = Product::create($data);

            Log::info('Product created successfully in both systems', [
                'local_id' => $product->id,
                'fiscalapi_id' => $data['fiscalapiId']
            ]);

            return $product;

        } catch (Exception $e) {
            Log::error('Failed to create product in FiscalAPI', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Update product in both local database and FiscalAPI
     */
    public function updateProduct(Product $product, array $data): Product
    {
        try {
            // Update in FiscalAPI first
            if ($product->fiscalapiId) {
                $fiscalApiData = $this->prepareFiscalApiData($data, $product->fiscalapiId);
                $apiResponse = $this->fiscalApi->getProductService()->update($fiscalApiData);
                $responseData = $apiResponse->getJson();

                if (!$responseData['succeeded']) {
                    throw new Exception('Failed to update product in FiscalAPI: ' . ($responseData['message'] ?? 'Unknown error'));
                }
            }

            // Update in local database
            $product->update($data);

            Log::info('Product updated successfully in both systems', [
                'local_id' => $product->id,
                'fiscalapi_id' => $product->fiscalapiId
            ]);

            return $product;

        } catch (Exception $e) {
            Log::error('Failed to update product in FiscalAPI', [
                'error' => $e->getMessage(),
                'product_id' => $product->id,
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Delete product from both local database and FiscalAPI
     */
    public function deleteProduct(Product $product): bool
    {
        try {
            // Delete from FiscalAPI first
            if ($product->fiscalapiId) {
                $apiResponse = $this->fiscalApi->getProductService()->delete($product->fiscalapiId);
                $responseData = $apiResponse->getJson();

                if (!$responseData['succeeded']) {
                    throw new Exception('Failed to delete product in FiscalAPI: ' . ($responseData['message'] ?? 'Unknown error'));
                }
            }

            // Delete from local database
            $product->delete();

            Log::info('Product deleted successfully from both systems', [
                'local_id' => $product->id,
                'fiscalapi_id' => $product->fiscalapiId
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('Failed to delete product in FiscalAPI', [
                'error' => $e->getMessage(),
                'product_id' => $product->id
            ]);
            throw $e;
        }
    }

    /**
     * Sync product from FiscalAPI to local database
     */
    public function syncFromFiscalApi(string $fiscalApiId): ?Product
    {
        try {
            $apiResponse = $this->fiscalApi->getProductService()->get($fiscalApiId, true);
            $responseData = $apiResponse->getJson();

            if (!$responseData['succeeded']) {
                Log::warning('Failed to fetch product from FiscalAPI', [
                    'fiscalapi_id' => $fiscalApiId,
                    'message' => $responseData['message'] ?? 'Unknown error'
                ]);
                return null;
            }

            $fiscalApiProduct = $responseData['data'];

            // Check if product already exists locally
            $existingProduct = Product::where('fiscalapiId', $fiscalApiId)->first();

            if ($existingProduct) {
                // Update existing product
                $existingProduct->update([
                    'description' => $fiscalApiProduct['description'],
                    'unitPrice' => $fiscalApiProduct['unitPrice'],
                    'sat_unit_measurement_id' => $fiscalApiProduct['satUnitMeasurementId'],
                    'sat_tax_object_id' => $fiscalApiProduct['satTaxObjectId'],
                    'sat_product_code_id' => $fiscalApiProduct['satProductCodeId'],
                ]);
                return $existingProduct;
            } else {
                // Create new product
                return Product::create([
                    'description' => $fiscalApiProduct['description'],
                    'unitPrice' => $fiscalApiProduct['unitPrice'],
                    'sat_unit_measurement_id' => $fiscalApiProduct['satUnitMeasurementId'],
                    'sat_tax_object_id' => $fiscalApiProduct['satTaxObjectId'],
                    'sat_product_code_id' => $fiscalApiProduct['satProductCodeId'],
                    'fiscalapiId' => $fiscalApiId,
                ]);
            }

        } catch (Exception $e) {
            Log::error('Failed to sync product from FiscalAPI', [
                'error' => $e->getMessage(),
                'fiscalapi_id' => $fiscalApiId
            ]);
            return null;
        }
    }

    /**
     * Sync all products from FiscalAPI
     */
    public function syncAllFromFiscalApi(): array
    {
        try {
            $page = 1;
            $syncedProducts = [];

            do {
                $apiResponse = $this->fiscalApi->getProductService()->list($page, 50);
                $responseData = $apiResponse->getJson();

                if (!$responseData['succeeded']) {
                    Log::error('Failed to fetch products from FiscalAPI', [
                        'page' => $page,
                        'message' => $responseData['message'] ?? 'Unknown error'
                    ]);
                    break;
                }

                foreach ($responseData['data']['items'] as $fiscalApiProduct) {
                    $syncedProduct = $this->syncFromFiscalApi($fiscalApiProduct['id']);
                    if ($syncedProduct) {
                        $syncedProducts[] = $syncedProduct;
                    }
                }

                $page++;

            } while ($responseData['data']['hasNextPage']);

            Log::info('Sync completed', [
                'total_synced' => count($syncedProducts)
            ]);

            return $syncedProducts;

        } catch (Exception $e) {
            Log::error('Failed to sync all products from FiscalAPI', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Prepare data for FiscalAPI requests
     */
    protected function prepareFiscalApiData(array $data, ?string $id = null): array
    {
        $fiscalApiData = [
            'description' => $data['description'],
            'unitPrice' => (float) $data['unitPrice'],
        ];

        // Add ID if updating
        if ($id) {
            $fiscalApiData['id'] = $id;
        }

        // Add SAT fields if present
        if (isset($data['sat_unit_measurement_id'])) {
            $fiscalApiData['satUnitMeasurementId'] = $data['sat_unit_measurement_id'];
        }

        if (isset($data['sat_tax_object_id'])) {
            $fiscalApiData['satTaxObjectId'] = $data['sat_tax_object_id'];
        }

        if (isset($data['sat_product_code_id'])) {
            $fiscalApiData['satProductCodeId'] = $data['sat_product_code_id'];
        }

        // Add default IVA tax if not specified
        if (!isset($data['productTaxes'])) {
            $fiscalApiData['productTaxes'] = [
                [
                    'rate' => 0.16,
                    'taxId' => '002', // IVA
                    'taxFlagId' => 'T', // Traslado
                    'taxTypeId' => 'Tasa',
                ]
            ];
        }

        return $fiscalApiData;
    }
}
