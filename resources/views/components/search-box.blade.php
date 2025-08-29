@props([
    'placeholder' => 'Buscar productos...',
    'id' => 'product-search'
])

<div {{ $attributes->merge(['class' => 'relative']) }}>
    <div class="relative">
        <input
            type="text"
            id="{{ $id }}"
            placeholder="{{ $placeholder }}"
            class="block w-full rounded-md border-0 py-3 pl-4 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6"
            autocomplete="off"
        >
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
            </svg>
        </div>
    </div>

    <!-- Search Results Dropdown -->
    <div id="{{ $id }}-results" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
        <!-- Results will be populated by JavaScript -->
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('{{ $id }}');
    const resultsContainer = document.getElementById('{{ $id }}-results');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            resultsContainer.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => {
            searchProducts(query);
        }, 300);
    });

    searchInput.addEventListener('focus', function() {
        if (this.value.trim().length >= 2) {
            resultsContainer.classList.remove('hidden');
        }
    });

    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
            resultsContainer.classList.add('hidden');
        }
    });

    function searchProducts(query) {
        fetch(`/pos/search-products?query=${encodeURIComponent(query)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.products.length > 0) {
                displayResults(data.products);
                resultsContainer.classList.remove('hidden');
            } else {
                resultsContainer.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error searching products:', error);
            resultsContainer.classList.add('hidden');
        });
    }

    function displayResults(products) {
        resultsContainer.innerHTML = '';

        products.forEach(product => {
            const resultItem = document.createElement('div');
            resultItem.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0';
            resultItem.innerHTML = `
                <div class="flex justify-between items-center">
                    <div>
                        <div class="font-medium text-gray-900">${product.description}</div>
                        <div class="text-sm text-gray-500">Stock: ${product.stock || 'N/A'}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-medium text-green-600">$${parseFloat(product.unitPrice).toFixed(2)}</div>
                    </div>
                </div>
            `;

            resultItem.addEventListener('click', () => {
                selectProduct(product);
                resultsContainer.classList.add('hidden');
                searchInput.value = '';
            });

            resultsContainer.appendChild(resultItem);
        });
    }

    function selectProduct(product) {
        // Dispatch custom event for product selection
        const event = new CustomEvent('productSelected', {
            detail: { product: product }
        });
        document.dispatchEvent(event);
    }
});
</script>
