<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SatUnitMeasurementCode;
use App\Models\SatTaxObjectCode;
use App\Models\SatProductCode;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\FiscalApiProductService;
use Exception;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $fiscalApiService;

    public function __construct(FiscalApiProductService $fiscalApiService)
    {
        $this->fiscalApiService = $fiscalApiService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with([
            'satUnitMeasurement',
            'satTaxObject',
            'satProductCode'
        ])->get();

        return view('components.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $satUnitMeasurements = SatUnitMeasurementCode::all();
        $satTaxObjects = SatTaxObjectCode::all();
        $satProductCodes = SatProductCode::all();

        return view('components.products.create', compact(
            'satUnitMeasurements',
            'satTaxObjects',
            'satProductCodes'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $product = $this->fiscalApiService->createProduct($request->validated());
            return redirect()->route('products.index')->with('success', 'Producto creado correctamente en ambos sistemas');
        } catch (Exception $e) {
            Log::error('Failed to create product', [
                'error' => $e->getMessage(),
                'data' => $request->validated()
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear el producto: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load([
            'satUnitMeasurement',
            'satTaxObject',
            'satProductCode'
        ]);

        return view('components.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $satUnitMeasurements = SatUnitMeasurementCode::all();
        $satTaxObjects = SatTaxObjectCode::all();
        $satProductCodes = SatProductCode::all();

        return view('components.products.edit', compact(
            'product',
            'satUnitMeasurements',
            'satTaxObjects',
            'satProductCodes'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            $product = $this->fiscalApiService->updateProduct($product, $request->validated());
            return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente en ambos sistemas');
        } catch (Exception $e) {
            Log::error('Failed to update product', [
                'error' => $e->getMessage(),
                'product_id' => $product->id,
                'data' => $request->validated()
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar el producto: ' . $e->getMessage()]);
        }
    }

        /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            $this->fiscalApiService->deleteProduct($product);
            return redirect()->route('products.index')->with('success', 'Producto eliminado correctamente de ambos sistemas');
        } catch (Exception $e) {
            Log::error('Failed to delete product', [
                'error' => $e->getMessage(),
                'product_id' => $product->id
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Error al eliminar el producto: ' . $e->getMessage()]);
        }
    }

    /**
     * Sync a specific product from FiscalAPI
     */
    public function syncFromFiscalApi(string $fiscalApiId)
    {
        try {
            $product = $this->fiscalApiService->syncFromFiscalApi($fiscalApiId);

            if ($product) {
                return redirect()->route('products.index')->with('success', 'Producto sincronizado correctamente desde FiscalAPI');
            } else {
                return redirect()->route('products.index')->with('warning', 'No se pudo sincronizar el producto desde FiscalAPI');
            }
        } catch (Exception $e) {
            Log::error('Failed to sync product from FiscalAPI', [
                'error' => $e->getMessage(),
                'fiscalapi_id' => $fiscalApiId
            ]);

            return redirect()->route('products.index')->with('error', 'Error al sincronizar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Sync all products from FiscalAPI
     */
    public function syncAllFromFiscalApi()
    {
        try {
            $syncedProducts = $this->fiscalApiService->syncAllFromFiscalApi();

            return redirect()->route('products.index')->with('success', 'Sincronización completada. ' . count($syncedProducts) . ' productos sincronizados');
        } catch (Exception $e) {
            Log::error('Failed to sync all products from FiscalAPI', [
                'error' => $e->getMessage()
            ]);

            return redirect()->route('products.index')->with('error', 'Error al sincronizar productos: ' . $e->getMessage());
        }
    }
}
