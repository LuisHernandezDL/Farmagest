@extends('layouts.app')

@php
    $editing = $purchase->exists;
@endphp

@section('title', $editing ? 'Editar compra | FarmaGest' : 'Nueva compra | FarmaGest')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                {{ $editing ? 'Editar compra' : 'Registrar compra' }}
            </h1>
            <p class="text-muted mb-0">
                Ingresa los datos de la compra de medicamentos.
            </p>
        </div>

        <a href="{{ route('purchases.index') }}"
           class="btn btn-outline-secondary">
            Volver a compras
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Revisa los siguientes datos:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST"
                  action="{{ $editing
                      ? route('purchases.update', $purchase)
                      : route('purchases.store') }}">

                @csrf

                @if($editing)
                    @method('PUT')
                @endif

                <div class="mb-4">
                    <label for="supplier_id" class="form-label">
                        Proveedor
                    </label>

                    <select name="supplier_id"
                            id="supplier_id"
                            class="form-select"
                            required>
                        <option value="">Selecciona un proveedor</option>

                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}"
                                @selected(old('supplier_id', $purchase->supplier_id) == $supplier->id)>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <h5 class="mb-3">Medicamentos de la compra</h5>

@php
    $items = old('items', $editing
        ? $purchase->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
        ])->toArray()
        : [['product_id' => '', 'quantity' => 1, 'unit_price' => '']]
    );
@endphp

<div id="purchase-items">
    @foreach($items as $index => $item)
        <div class="row g-3 mb-3 purchase-item">
            <div class="col-md-5">
                <label class="form-label">Medicamento</label>
                <select name="items[{{ $index }}][product_id]"
                        class="form-select" required>
                    <option value="">Selecciona un medicamento</option>
                    @foreach($products as $product)
    <option value="{{ $product->id }}"
        data-purchase-price="{{ $product->purchase_price }}"
        @selected(($item['product_id'] ?? '') == $product->id)>
        {{ $product->name }}
    </option>
@endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Cantidad</label>
                <input type="number"
                       name="items[{{ $index }}][quantity]"
                       class="form-control quantity-input"
                       min="1"
                       value="{{ $item['quantity'] ?? 1 }}"
                       required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Precio unitario (Q)</label>
                <input type="number"
                       name="items[{{ $index }}][unit_price]"
                       class="form-control price-input"
                       min="0"
                       step="0.01"
                       value="{{ $item['unit_price'] ?? '' }}"
                       required>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="button"
                        class="btn btn-outline-danger w-100 remove-item">
                    Quitar
                </button>
            </div>
        </div>
    @endforeach
</div>

<button type="button" id="add-item" class="btn btn-outline-primary">
    + Agregar medicamento
</button>

<div class="text-end mt-4">
    <h5>Total estimado: <span id="purchase-total">Q 0.00</span></h5>
</div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        {{ $editing ? 'Guardar cambios' : 'Registrar compra' }}
                    </button>

                    <a href="{{ route('purchases.index') }}"
                       class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('purchase-items');
    const addButton = document.getElementById('add-item');
    const totalElement = document.getElementById('purchase-total');

    let nextIndex = {{ count($items) }};

    function updateTotal() {
        let total = 0;

        container.querySelectorAll('.purchase-item').forEach(row => {
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            total += quantity * price;
        });

        totalElement.textContent = 'Q ' + total.toFixed(2);
    }

    function updatePurchasePrice(select) {
    const row = select.closest('.purchase-item');
    const priceInput = row.querySelector('.price-input');

    const selectedOption = select.options[select.selectedIndex];
    const price = selectedOption.dataset.purchasePrice;

    priceInput.value = price !== undefined ? Number(price).toFixed(2) : '';

    updateTotal();
}

container.addEventListener('change', function (event) {
    if (event.target.matches('select[name$="[product_id]"]')) {
        updatePurchasePrice(event.target);
    }
});

    addButton.addEventListener('click', function () {
        const firstRow = container.querySelector('.purchase-item');
        const newRow = firstRow.cloneNode(true);

        newRow.querySelectorAll('[name]').forEach(input => {
            input.name = input.name.replace(/items\[\d+\]/, 'items[' + nextIndex + ']');

            if (input.tagName === 'SELECT') {
                input.value = '';
            } else if (input.classList.contains('quantity-input')) {
                input.value = 1;
            } else {
                input.value = '';
            }
        });

        container.appendChild(newRow);
        nextIndex++;
        updateTotal();
    });

    container.addEventListener('click', function (event) {
        if (event.target.classList.contains('remove-item')) {
            const rows = container.querySelectorAll('.purchase-item');

            if (rows.length > 1) {
                event.target.closest('.purchase-item').remove();
                updateTotal();
            } else {
            const existingAlert = document.getElementById('purchase-item-alert');

if (!existingAlert) {
    const warning = document.createElement('div');

    warning.id = 'purchase-item-alert';
    warning.className = 'alert alert-warning alert-dismissible fade show mt-3';
    warning.setAttribute('role', 'alert');

    warning.innerHTML = `
        <strong>Atención:</strong> La compra debe contener al menos un medicamento.
        <button type="button" class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Cerrar"></button>
    `;

    container.insertAdjacentElement('afterend', warning);
}
            }
        }
    });

    container.addEventListener('input', updateTotal);
    updateTotal();
});
</script>