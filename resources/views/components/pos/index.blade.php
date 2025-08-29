<x-layout.app-layout title="Punto de Venta">
    <x-layout.main-content>
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Left Panel - Item Management -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Top Search and Action Bar -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <x-search-box placeholder="Escanear o buscar producto..." />
                        </div>
                        <div class="flex gap-3">
                            <button
                                type="button"
                                id="cancel-sale-btn"
                                class="px-4 py-2 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            >
                                X CANCELAR VENTA
                            </button>
                            <button
                                type="button"
                                id="pause-sale-btn"
                                class="px-4 py-2 bg-yellow-500 text-white font-medium rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2"
                            >
                                II PAUSAR
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Company and Customer Selection -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-person-select
                            name="company"
                            label="Empresa (Emisor)"
                            :options="$companies"
                            required
                        />
                        <x-person-select
                            name="customer"
                            label="Cliente (Receptor)"
                            :options="$customers"
                            required
                        />
                    </div>
                </div>

                <!-- Item List Header -->
                <div class="bg-green-100 rounded-lg p-4">
                    <div class="grid grid-cols-12 gap-4 text-sm font-medium text-gray-900">
                        <div class="col-span-4">Producto</div>
                        <div class="col-span-2 text-center">Precio</div>
                        <div class="col-span-2 text-center">Cant.</div>
                        <div class="col-span-2 text-center">Desc.</div>
                        <div class="col-span-2 text-right">Subtotal</div>
                    </div>
                </div>

                <!-- Current Sale Items -->
                <div id="order-items" class="space-y-2">
                    <!-- Items will be populated by JavaScript -->
                </div>

            </div>

            <!-- Right Panel - Sale Summary & Payment -->
            <div class="lg:col-span-1">
                <x-order-summary />
            </div>
        </div>
    </x-main-content>
</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentOrder = @json($order ?? null);
    let currentOrderItems = [];

    // Initialize POS
    initializePOS();

    // Event listeners
    document.getElementById('cancel-sale-btn').addEventListener('click', cancelSale);
    document.getElementById('pause-sale-btn').addEventListener('click', pauseSale);
    document.getElementById('end-sale-btn').addEventListener('click', endSale);

    // Company and customer change listeners
    document.querySelector('select[name="company"]').addEventListener('change', updateCompany);
    document.querySelector('select[name="customer"]').addEventListener('change', updateCustomer);

    // Product selection listener
    document.addEventListener('productSelected', handleProductSelected);

    // Handle navigation away from POS
    window.addEventListener('beforeunload', handleBeforeUnload);
    window.addEventListener('pagehide', handlePageHide);

    function initializePOS() {
        // Order is already created by the controller
        if (currentOrder) {
            console.log('Orden existente cargada:', currentOrder);
            // Load existing order items from database
            loadOrderItems();
            updateOrderSummary(currentOrder);
        }
    }

    function loadOrderItems() {
        if (!currentOrder) return;

        fetch(`/pos/get-order-items/${currentOrder.id}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentOrderItems = data.items || [];
                updateOrderItemsDisplay();
                // Update order items status in order summary
                if (window.updateOrderItemsStatus) {
                    window.updateOrderItemsStatus(currentOrderItems.length > 0);
                }
                console.log('Items de orden cargados:', currentOrderItems);
            } else {
                console.error('Error al cargar items:', data.message);
            }
        })
        .catch(error => {
            console.error('Error al cargar items:', error);
        });
    }

    function handleProductSelected(event) {
        const product = event.detail.product;
        if (!currentOrder) {
            alert('Error: No hay una orden activa');
            return;
        }

        addProductToOrder(product);
    }

    function addProductToOrder(product) {
        fetch('/pos/add-product', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                orderId: currentOrder.id,
                productId: product.id,
                quantity: 1,
                discountPercentage: 0
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentOrder = data.order;
                // Update currentOrderItems with the fresh data from the response
                currentOrderItems = data.order.items || [];
                updateOrderSummary(currentOrder);
                updateOrderItemsDisplay();
                console.log('Producto agregado:', data.orderItem);
            } else {
                alert('Error al agregar producto: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al agregar producto');
        });
    }

    function updateOrderItemsDisplay() {
        const container = document.getElementById('order-items');
        container.innerHTML = '';

        if (currentOrderItems.length === 0) {
            container.innerHTML = '<div class="text-center py-8 text-gray-500">No hay productos en esta venta</div>';
            // Update order items status in order summary
            if (window.updateOrderItemsStatus) {
                window.updateOrderItemsStatus(false);
            }
            return;
        }

        currentOrderItems.forEach(item => {
            const itemElement = createOrderItemElement(item);
            container.appendChild(itemElement);
        });

        // Update order items status in order summary
        if (window.updateOrderItemsStatus) {
            window.updateOrderItemsStatus(true);
        }
    }

    function createOrderItemElement(item) {
        const div = document.createElement('div');
        div.className = 'bg-white rounded-lg shadow-sm border border-gray-200 p-4';
        div.innerHTML = `
            <div class="grid grid-cols-12 gap-4 items-center">
                <div class="col-span-4">
                    <div class="font-medium text-gray-900">${item.product.description}</div>
                    <div class="text-sm text-gray-500">Stock: ${item.product.stock || 'N/A'}</div>
                </div>
                <div class="col-span-2 text-center text-gray-900">$${parseFloat(item.unit_price).toFixed(2)}</div>
                <div class="col-span-2 text-center">
                    <input type="number" min="1" value="${item.quantity}"
                           class="w-16 text-center border-gray-300 rounded-md text-sm"
                           data-item-id="${item.id}"
                           onchange="updateItemQuantity(${item.id}, this.value)">
                </div>
                <div class="col-span-2 text-center">
                    <input type="number" min="0" max="100" step="0.01" value="${item.discount_percentage || 0}"
                           class="w-16 text-center border-gray-300 rounded-md text-sm"
                           data-item-id="${item.id}"
                           onchange="updateItemDiscount(${item.id}, this.value)">
                </div>
                <div class="col-span-2 text-right font-medium text-gray-900">$${parseFloat(item.subtotal).toFixed(2)}</div>
                <div class="col-span-12 flex justify-end">
                    <button type="button"
                            onclick="removeItem(${item.id})"
                            class="text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 11-2 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        `;
        return div;
    }

    function clearOrderItems() {
        document.getElementById('order-items').innerHTML = '';
        // Update order items status in order summary
        if (window.updateOrderItemsStatus) {
            window.updateOrderItemsStatus(false);
        }
    }

    function updateCompany() {
        if (currentOrder) {
            updateOrderField('issuerId', this.value);
        }
    }

    function updateCustomer() {
        if (currentOrder) {
            updateOrderField('recipientId', this.value);
        }
    }

    function updateOrderField(field, value) {
        if (!currentOrder) return;

        const updateData = {};
        updateData[field] = value;

        fetch('/pos/update-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                orderId: currentOrder.id,
                ...updateData
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentOrder = data.order;
                console.log('Orden actualizada:', currentOrder);
            } else {
                alert('Error al actualizar la orden: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al actualizar la orden');
        });
    }

    function cancelSale() {
        if (!currentOrder) {
            alert('No hay una venta activa para cancelar');
            return;
        }

        if (confirm('¿Estás seguro de que quieres cancelar esta venta?')) {
            fetch('/pos/cancel-sale', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    orderId: currentOrder.id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentOrder = null;
                    currentOrderItems = [];
                    updateOrderSummary(null);
                    clearOrderItems();
                    window.resetPaymentInputs();
                    alert('Venta cancelada correctamente');
                } else {
                    alert('Error al cancelar la venta: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cancelar la venta');
            });
        }
    }

    function pauseSale() {
        alert('Función de pausar venta en desarrollo');
    }

    function endSale() {
        if (!currentOrder) {
            alert('No hay una venta activa para finalizar');
            return;
        }

        // Check if there are items in the current order
        if (currentOrderItems.length === 0) {
            alert('No se puede finalizar una venta sin productos');
            return;
        }

        if (confirm('¿Estás seguro de que quieres finalizar esta venta?')) {
            fetch('/pos/end-sale', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    orderId: currentOrder.id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Venta completada correctamente');
                    // Reset for new sale
                    currentOrder = null;
                    currentOrderItems = [];
                    updateOrderSummary(null);
                    clearOrderItems();
                    window.resetPaymentInputs();

                    // Create new order
                    setTimeout(() => {
                        createNewOrder();
                    }, 1000);
                } else {
                    alert('Error al finalizar la venta: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al finalizar la venta');
            });
        }
    }

    function createNewOrder() {
        fetch('/pos/create-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                issuerId: null,
                recipientId: null
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentOrder = data.order;
                currentOrderItems = [];
                updateOrderItemsDisplay();
                console.log('Nueva orden creada:', currentOrder);
            } else {
                console.error('Error al crear nueva orden:', data.message);
            }
        })
        .catch(error => {
            console.error('Error al crear nueva orden:', error);
        });
    }

    // Handle navigation away from POS
    function handleBeforeUnload(event) {
        if (currentOrder && currentOrderItems.length > 0) {
            event.preventDefault();
            event.returnValue = 'Tienes una venta en progreso. ¿Estás seguro de que quieres salir?';
            return event.returnValue;
        }
    }

    function handlePageHide() {
        if (currentOrder && currentOrderItems.length > 0) {
            // Automatically cancel the sale if user navigates away
            fetch('/pos/cancel-sale', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    orderId: currentOrder.id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Venta cancelada automáticamente al navegar');
                }
            })
            .catch(error => {
                console.error('Error al cancelar venta automáticamente:', error);
            });
        }
    }

    // Global functions for inline event handlers
    window.updateItemQuantity = function(itemId, quantity) {
        updateItemField(itemId, 'quantity', parseInt(quantity));
    };

    window.updateItemDiscount = function(itemId, discount) {
        updateItemField(itemId, 'discountPercentage', parseFloat(discount));
    };

    window.removeItem = function(itemId) {
        if (confirm('¿Estás seguro de que quieres remover este producto?')) {
            fetch('/pos/remove-product', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    orderItemId: itemId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentOrder = data.order;
                    currentOrderItems = data.order.items || [];
                    updateOrderSummary(currentOrder);
                    updateOrderItemsDisplay();
                } else {
                    alert('Error al remover producto: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al remover producto');
            });
        }
    };

    function updateItemField(itemId, field, value) {
        const updateData = {};
        updateData[field] = value;

        if (field === 'quantity') {
            fetch('/pos/update-quantity', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    orderItemId: itemId,
                    quantity: value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentOrder = data.order;
                    currentOrderItems = data.order.items || [];
                    updateOrderSummary(currentOrder);
                    updateOrderItemsDisplay();
                } else {
                    alert('Error al actualizar cantidad: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar cantidad');
            });
        } else if (field === 'discountPercentage') {
            fetch('/pos/update-discount', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    orderItemId: itemId,
                    discountPercentage: value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentOrder = data.order;
                    currentOrderItems = data.order.items || [];
                    updateOrderSummary(currentOrder);
                    updateOrderItemsDisplay();
                } else {
                    alert('Error al actualizar descuento: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar descuento');
            });
        }
    }
});
</script>
