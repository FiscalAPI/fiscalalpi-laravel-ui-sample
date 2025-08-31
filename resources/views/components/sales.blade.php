@props([
    'orders' => collect()
])

<div {{ $attributes->merge(['class' => 'bg-white shadow-sm rounded-lg border border-gray-200']) }}>
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Ventas</h3>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Total: {{ $orders->count() }} ventas</span>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        ID Venta
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Cliente
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Total
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estado
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Fecha
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ $order->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($order->recipient)
                                <div>
                                    <div class="font-medium text-gray-900">
                                        {{ $order->recipient->legalName ?? 'Cliente sin nombre' }}
                                    </div>
                                    @if($order->recipient->tin)
                                        <div class="text-xs text-gray-500">
                                            RFC: {{ $order->recipient->tin }}
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400">Cliente no asignado</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="text-right">
                                <div class="font-medium text-gray-900">
                                    ${{ number_format($order->total, 2) }}
                                </div>
                                @if($order->discounts > 0)
                                    <div class="text-xs text-red-600">
                                        -${{ number_format($order->discounts, 2) }} desc.
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <!-- Botón Facturar -->
                                <button
                                    type="button"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                    {{ $order->invoice_id ? 'disabled' : '' }}
                                    onclick="generateInvoice({{ $order->id }})"
                                >
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Facturar
                                </button>

                                <!-- Botón Ver PDF -->
                                <button
                                    type="button"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                    {{ !$order->invoice_id ? 'disabled' : '' }}
                                    onclick="viewInvoicePdf('{{ $order->invoice_id }}')"
                                >
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Ver PDF
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-lg font-medium text-gray-900 mb-2">No hay ventas</p>
                                <p class="text-sm text-gray-500">Las ventas aparecerán aquí cuando se creen</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- JavaScript para manejar las acciones -->
<script>
function generateInvoice(orderId) {
    if (confirm('¿Estás seguro de que quieres generar la factura para esta venta?')) {
        // Mostrar indicador de carga
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = `
            <svg class="animate-spin w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Generando...
        `;
        button.disabled = true;

        // Llamada a la API para generar factura
        fetch(`/api/orders/${orderId}/generate-invoice`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recargar la página para mostrar el nuevo estado
                window.location.reload();
            } else {
                alert('Error al generar la factura: ' + data.message);
                // Restaurar el botón
                button.innerHTML = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al generar la factura. Por favor, inténtalo de nuevo.');
            // Restaurar el botón
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
}

function viewInvoicePdf(invoiceId) {
    if (!invoiceId) {
        alert('No hay factura disponible para esta venta.');
        return;
    }

    // Abrir el PDF en una nueva pestaña
    const pdfUrl = `/api/invoices/${invoiceId}/pdf`;
    window.open(pdfUrl, '_blank');
}
</script>
