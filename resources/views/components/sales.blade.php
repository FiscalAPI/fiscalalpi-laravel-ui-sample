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
                                @if(!$order->invoice_id)
                                    <!-- Botón Facturar -->
                                    <button
                                        type="button"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                        onclick="generateInvoice({{ $order->id }})"
                                    >
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Facturar
                                    </button>
                                @else
                                    <!-- Botones para facturas existentes -->
                                    <div class="flex items-center space-x-1">
                                        <!-- Botón Ver PDF -->
                                        <button
                                            type="button"
                                            class="inline-flex items-center px-2 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                            onclick="viewInvoicePdf('{{ $order->invoice_id }}')"
                                            title="Ver PDF"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        <!-- Botón Descargar PDF -->
                                        <button
                                            type="button"
                                            class="inline-flex items-center px-2 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                            onclick="downloadInvoicePdf('{{ $order->invoice_id }}')"
                                            title="Descargar PDF"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </button>

                                        <!-- Botón Descargar XML -->
                                        <button
                                            type="button"
                                            class="inline-flex items-center px-2 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                            onclick="downloadInvoiceXml('{{ $order->invoice_id }}')"
                                            title="Descargar XML"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </button>

                                        <!-- Botón Enviar por Correo -->
                                        <button
                                            type="button"
                                            class="inline-flex items-center px-2 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                            onclick="sendInvoiceByEmail('{{ $order->invoice_id }}', '{{ $order->recipient->email ?? '' }}')"
                                            title="Enviar por correo"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                    </div>
                                @endif
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

<!-- Modal para envío por correo -->
<div id="emailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg p-6 w-96 max-w-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Enviar factura por correo</h3>
                <button onclick="closeEmailModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="emailForm" class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                    <input type="email" id="email" name="email" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEmailModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript para manejar las acciones -->
<script>
let currentInvoiceId = null;

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
                // Mostrar mensaje de éxito
                showNotification('Factura generada exitosamente', 'success');
                // Recargar la página para mostrar el nuevo estado
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showNotification('Error al generar la factura: ' + data.message, 'error');
                // Restaurar el botón
                button.innerHTML = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al generar la factura. Por favor, inténtalo de nuevo.', 'error');
            // Restaurar el botón
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
}

function viewInvoicePdf(invoiceId) {
    if (!invoiceId) {
        showNotification('No hay factura disponible para esta venta.', 'error');
        return;
    }

    // Abrir el PDF en una nueva pestaña
    const pdfUrl = `/api/invoices/${invoiceId}/pdf`;
    window.open(pdfUrl, '_blank');
}

function downloadInvoicePdf(invoiceId) {
    if (!invoiceId) {
        showNotification('No hay factura disponible para esta venta.', 'error');
        return;
    }

    // Descargar el PDF
    const downloadUrl = `/sales/invoice/${invoiceId}/download-pdf`;
    window.open(downloadUrl, '_blank');
}

function downloadInvoiceXml(invoiceId) {
    if (!invoiceId) {
        showNotification('No hay factura disponible para esta venta.', 'error');
        return;
    }

    // Obtener XML y descargar
    fetch(`/api/invoices/${invoiceId}/xml`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Crear y descargar archivo XML
                const xmlContent = atob(data.xml_data.base64File);
                const blob = new Blob([xmlContent], { type: 'application/xml' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = data.xml_data.fileName || 'invoice.xml';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);

                showNotification('XML descargado exitosamente', 'success');
            } else {
                showNotification('Error al obtener el XML: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al descargar el XML', 'error');
        });
}

function sendInvoiceByEmail(invoiceId, defaultEmail = '') {
    if (!invoiceId) {
        showNotification('No hay factura disponible para esta venta.', 'error');
        return;
    }

    currentInvoiceId = invoiceId;

    // Prellenar email si está disponible
    if (defaultEmail) {
        document.getElementById('email').value = defaultEmail;
    } else {
        document.getElementById('email').value = '';
    }

    // Mostrar modal
    document.getElementById('emailModal').classList.remove('hidden');
}

function closeEmailModal() {
    document.getElementById('emailModal').classList.add('hidden');
    currentInvoiceId = null;
}

// Manejar envío del formulario de email
document.getElementById('emailForm').addEventListener('submit', function(e) {
    e.preventDefault();

    if (!currentInvoiceId) {
        showNotification('Error: ID de factura no válido', 'error');
        return;
    }

    const email = document.getElementById('email').value;

    // Mostrar indicador de carga
    const submitButton = e.target.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = `
        <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Enviando...
    `;
    submitButton.disabled = true;

    // Enviar factura por correo
    fetch(`/api/invoices/${currentInvoiceId}/send-email`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ email: email })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Factura enviada por correo exitosamente', 'success');
            closeEmailModal();
        } else {
            showNotification('Error al enviar la factura: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al enviar la factura', 'error');
    })
    .finally(() => {
        // Restaurar botón
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
});

function showNotification(message, type = 'info') {
    // Crear notificación
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    notification.innerHTML = message;

    document.body.appendChild(notification);

    // Remover después de 5 segundos
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 5000);
}
</script>
