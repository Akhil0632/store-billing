@extends('layouts.app')

@section('title', 'New Order')
@section('header-title', 'Store Billing — New Order')
@section('header-subtitle', 'ORDER CREATION')

@section('content')
    <form id="orderForm" action="{{ route('orders.store') }}" method="POST">
        @csrf
        <div class="container">

            <!-- LEFT COLUMN -->
            <div class="left-column">

                <!-- Customer Section -->
                <div class="section-title">Customer</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="customer_email" id="customer_email"
                               placeholder="e.g. thomas@example.com" required>
                    </div>
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="customer_name" id="customer_name"
                               placeholder="auto-filled if email exists" required>
                    </div>
                </div>

                <!-- Products Section -->
                <div class="section-title" style="margin-top: 20px;">Products</div>
                <table class="products-table" id="productsTable">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th style="width: 60px;">Qty</th>
                            <th style="width: 80px;">Price</th>
                            <th style="width: 100px;">Line Total</th>
                            <th style="width: 40px;"></th>
                        </tr>
                    </thead>
                    <tbody id="productRows">
                        <!-- Rows added dynamically -->
                    </tbody>
                </table>
                <button type="button" class="btn-add" id="addProductBtn">
                    + Add Product
                </button>

                <!-- Payment Section -->
                <div class="section-title" style="margin-top: 30px;">Payment</div>
                <div class="payment-box">
                    <div class="payment-row">
                        <span>Subtotal</span>
                        <span id="subtotalDisplay">€0.00</span>
                    </div>
                    <div class="payment-row">
                        <span>Tax</span>
                        <span id="taxDisplay">€0.00</span>
                    </div>
                    <div class="payment-row total">
                        <span>Grand Total</span>
                        <span id="grandTotalDisplay">€0.00</span>
                    </div>

                    <div class="amount-given-input">
                        <label>Amount Given by Customer</label>
                        <input type="number" name="amount_given" id="amount_given"
                               placeholder="€250" step="0.01" required>
                    </div>

                    <div class="balance-return">
                        Balance to Return: <span id="changeDisplay">€0.00</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="right-column">

                <!-- Low Stock Alert -->
                <div class="alert-box">
                    <h3>⚠ Low Stock Alert</h3>
                    <ul>
                        @forelse($lowStockProducts as $product)
                            <li>{{ $product->name }} — {{ $product->stock }} units left</li>
                        @empty
                            <li>All stock levels are healthy.</li>
                        @endforelse
                    </ul>
                </div>

                <!-- Generate Bill Button -->
                <div>
                    <button type="submit" class="btn-generate">Generate Bill</button>
                    <div class="balance-return" style="text-align: left; margin-top: 5px; font-size: 0.75rem;">
                        → saves bill on page 7<br>
                        → emails PDF to customer
                    </div>
                </div>

            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    const products = @json($products);

    document.addEventListener('DOMContentLoaded', function () {
        const productRows      = document.getElementById('productRows');
        const addProductBtn    = document.getElementById('addProductBtn');
        const amountGivenInput = document.getElementById('amount_given');

        // Fail loudly instead of silently doing nothing
        if (!productRows)      { console.error('Missing #productRows');      return; }
        if (!addProductBtn)    { console.error('Missing #addProductBtn');    return; }
        if (!amountGivenInput) { console.error('Missing #amount_given');     return; }

        console.log('Order form JS loaded. Products:', products.length);

        let rowIndex = 0;

        function addProductRow() {
            const row = document.createElement('tr');

            let productOptions = '<option value="">Select Product</option>';
            products.forEach(p => {
                productOptions += `<option value="${p.id}" data-price="${p.price}" data-tax="${p.tax_percentage}" data-stock="${p.stock}">${p.name} (Stock: ${p.stock})</option>`;
            });

            row.innerHTML = `
                <td>
                    <select name="items[${rowIndex}][product_id]" class="product-select" required>
                        ${productOptions}
                    </select>
                </td>
                <td>
                    <input type="number" name="items[${rowIndex}][quantity]" class="qty-input" value="1" min="1" required>
                </td>
                <td class="price-display">€0.00</td>
                <td class="line-total-display">€0.00</td>
                <td>
                    <button type="button" class="remove-row"
                            style="background:none; border:none; color:red; cursor:pointer; font-weight:bold;">X</button>
                </td>
            `;

            row.querySelector('.remove-row').addEventListener('click', function () {
                if (document.querySelectorAll('#productRows tr').length > 1) {
                    row.remove();
                    calculateTotals();
                } else {
                    alert("At least one product line is required.");
                }
            });

            productRows.appendChild(row);
            rowIndex++;
        }

        function calculateTotals() {
            let subtotal = 0;
            let taxTotal = 0;

            document.querySelectorAll('#productRows tr').forEach(row => {
                const select   = row.querySelector('.product-select');
                const qtyInput = row.querySelector('.qty-input');
                const priceEl  = row.querySelector('.price-display');
                const lineEl   = row.querySelector('.line-total-display');

                const opt      = select.options[select.selectedIndex];
                const price    = parseFloat(opt.dataset.price) || 0;
                const taxRate  = parseFloat(opt.dataset.tax)   || 0;
                const qty      = parseInt(qtyInput.value)      || 0;

                const lineTotal = price * qty;
                const lineTax   = lineTotal * (taxRate / 100);

                priceEl.textContent = `€${price.toFixed(2)}`;
                lineEl.textContent  = `€${lineTotal.toFixed(2)}`;

                subtotal += lineTotal;
                taxTotal += lineTax;
            });

            const grandTotal = subtotal + taxTotal;

            document.getElementById('subtotalDisplay').textContent   = `€${subtotal.toFixed(2)}`;
            document.getElementById('taxDisplay').textContent        = `€${taxTotal.toFixed(2)}`;
            document.getElementById('grandTotalDisplay').textContent = `€${grandTotal.toFixed(2)}`;

            calculateChange();
        }

        function calculateChange() {
            const grandTotal = parseFloat(
                document.getElementById('grandTotalDisplay').textContent.replace('€', '')
            ) || 0;
            const amountGiven = parseFloat(amountGivenInput.value) || 0;
            const change = amountGiven - grandTotal;

            const changeDisplay = document.getElementById('changeDisplay');
            changeDisplay.textContent = change >= 0
                ? `€${change.toFixed(2)}`
                : `-€${Math.abs(change).toFixed(2)}`;
            changeDisplay.style.color = change >= 0 ? '#15803d' : 'red';
        }

        // Wire everything up
        addProductBtn.addEventListener('click', addProductRow);
        productRows.addEventListener('input',  calculateTotals);
        productRows.addEventListener('change', calculateTotals);
        amountGivenInput.addEventListener('input', calculateChange);

        // Add the first row
        addProductRow();
    });
</script>
@endpush