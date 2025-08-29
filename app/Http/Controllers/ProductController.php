<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SatUnitMeasurementCode;
use App\Models\SatTaxObjectCode;
use App\Models\SatProductCode;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
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
        $product = Product::create($request->validated());
        return redirect()->route('products.index')->with('success', 'Producto creado correctamente');
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
        $product->update($request->validated());
        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Producto eliminado correctamente');
    }
}
