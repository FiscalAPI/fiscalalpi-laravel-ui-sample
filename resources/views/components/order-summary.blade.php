@props([
    'order' => null,
    'orderId' => null
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-sm border border-gray-200 p-6']) }}>
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumen de la Venta</h3>

    <!-- Order Summary -->
    <div class="space-y-3 mb-6">
        <div class="flex justify-between text-sm">
            <span class="text-gray-600">Subtotal:</span>
            <span class="font-medium text-gray-900" id="order-subtotal">$0.00</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-gray-600">Descuentos:</span>
            <span class="font-medium text-red-600" id="order-discounts">-$0.00</span>
        </div>
        <div class="flex justify-between text-lg font-bold border-t pt-3">
            <span class="text-red-600">TOTAL:</span>
            <span class="text-red-600" id="order-total">$0.00</span>
        </div>
    </div>

    <!-- Payment Status -->
    <div class="space-y-3 mb-6">
        <div class="flex justify-between text-sm">
            <span class="text-gray-600">PAGADO:</span>
            <span class="font-bold text-gray-900" id="order-paid">$0.00</span>
        </div>
        <div class="flex justify-between text-lg font-bold">
            <span class="text-red-600">DEBE:</span>
            <span class="text-red-600" id="order-due">$0.00</span>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="mb-6">
        <h4 class="text-sm font-medium text-gray-900 mb-3">AGREGAR PAGO</h4>
        <div class="space-y-2">
            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"/>
                    </svg>
                    <span class="text-sm text-gray-700">EFECTIVO</span>
                </div>
                <input type="number" step="0.01" min="0" class="w-20 text-right border-0 bg-transparent text-sm font-medium text-gray-900" id="cash-payment" placeholder="$0.00">
            </div>

            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"/>
                    </svg>
                    <span class="text-sm text-gray-700">TARJETA DE CRÉDITO</span>
                </div>
                <input type="number" step="0.01" min="0" class="w-20 text-right border-0 bg-transparent text-sm font-medium text-gray-900" id="credit-payment" placeholder="$0.00">
            </div>

            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"/>
                    </svg>
                    <span class="text-sm text-gray-700">TARJETA DE DÉBITO</span>
                </div>
                <input type="number" step="0.01" min="0" class="w-20 text-right border-0 bg-transparent text-sm font-medium text-gray-900" id="debit-payment" placeholder="$0.00">
            </div>

            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"/>
                    </svg>
                    <span class="text-sm text-gray-700">CHEQUE</span>
                </div>
                <input type="number" step="0.01" min="0" class="w-20 text-right border-0 bg-transparent text-sm font-medium text-gray-900" id="check-payment" placeholder="$0.00">
            </div>

            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <span class="text-sm text-gray-700">TARJETA DE REGALO</span>
                </div>
                <input type="number" step="0.01" min="0" class="w-20 text-right border-0 bg-transparent text-sm font-medium text-gray-900" id="gift-payment" placeholder="$0.00">
            </div>
        </div>
    </div>

    <!-- End Sale Button -->
    <button
        type="button"
        id="end-sale-btn"
        class="w-full bg-green-600 text-white font-medium py-3 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
        disabled
    >
        FINALIZAR VENTA
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentInputs = document.querySelectorAll('input[id$="-payment"]');
    const endSaleBtn = document.getElementById('end-sale-btn');

    // Update totals when payment amounts change
    paymentInputs.forEach(input => {
        input.addEventListener('input', updatePaymentTotals);
    });

    function updatePaymentTotals() {
        let totalPaid = 0;
        paymentInputs.forEach(input => {
            const amount = parseFloat(input.value) || 0;
            totalPaid += amount;
        });

        const total = parseFloat(document.getElementById('order-total').textContent.replace('$', '')) || 0;
        const due = Math.max(0, total - totalPaid);

        document.getElementById('order-paid').textContent = `$${totalPaid.toFixed(2)}`;
        document.getElementById('order-due').textContent = `$${due.toFixed(2)}`;

        // Enable/disable end sale button
        endSaleBtn.disabled = due > 0;
    }

    // Function to update order summary from external data
    window.updateOrderSummary = function(orderData) {
        if (orderData) {
            document.getElementById('order-subtotal').textContent = `$${parseFloat(orderData.subtotal || 0).toFixed(2)}`;
            document.getElementById('order-discounts').textContent = `-$${parseFloat(orderData.discounts || 0).toFixed(2)}`;
            document.getElementById('order-total').textContent = `$${parseFloat(orderData.total || 0).toFixed(2)}`;
            document.getElementById('order-paid').textContent = `$${parseFloat(orderData.paid || 0).toFixed(2)}`;
            document.getElementById('order-due').textContent = `$${parseFloat(orderData.due || 0).toFixed(2)}`;

            updatePaymentTotals();
        }
    };

    // Function to reset payment inputs
    window.resetPaymentInputs = function() {
        paymentInputs.forEach(input => {
            input.value = '';
        });
        updatePaymentTotals();
    };
});
</script>
