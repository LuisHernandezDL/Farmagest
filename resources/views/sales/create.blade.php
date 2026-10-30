@extends('layouts.app')
@section('title', 'Registrar venta | FarmaGest')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <div class="app-eyebrow">PUNTO DE VENTA</div>
        <h1 class="app-page-title mb-1">Nueva venta</h1>
        <p class="text-muted mb-0">Selecciona los medicamentos y confirma el total.</p>
    </div>
    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">Volver al historial</a>
</div>

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <strong>Revisa los datos de la venta:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if ($products->isEmpty())
    <div class="alert alert-warning">No hay medicamentos con existencias disponibles. Registra productos antes de realizar una venta.</div>
@endif

<form method="POST" action="{{ route('sales.store') }}" id="saleForm">
    @csrf
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h2 class="h5 mb-0">Medicamentos</h2>
                <button type="button" class="btn btn-outline-success btn-sm" id="addSaleItem" @disabled($products->isEmpty())>
                    <i class="bi bi-plus-circle me-1" aria-hidden="true"></i> Agregar medicamento
                </button>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width:260px">Medicamento</th>
                            <th style="width:115px">Cantidad</th>
                            <th>Precio unitario</th>
                            <th>Disponible</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">Quitar</th>
                        </tr>
                    </thead>
                    <tbody id="saleItems">
                        @foreach (old('items', [['product_id' => '', 'quantity' => 1]]) as $index => $oldItem)
                            <tr class="sale-row">
                                <td>
                                    <select class="form-select sale-product" name="items[{{ $index }}][product_id]" required>
                                        <option value="">Selecciona un medicamento</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                data-price="{{ $product->sale_price }}"
                                                data-stock="{{ $product->stock }}"
                                                @selected((string) ($oldItem['product_id'] ?? '') === (string) $product->id)>
                                                {{ $product->name }} ({{ $product->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input class="form-control sale-quantity" type="number" name="items[{{ $index }}][quantity]"
                                        min="1" max="10000" step="1" value="{{ $oldItem['quantity'] ?? 1 }}" required>
                                </td>
                                <td class="sale-unit-price text-nowrap">Q 0.00</td>
                                <td class="sale-available text-nowrap">—</td>
                                <td class="sale-subtotal text-end fw-semibold text-nowrap">Q 0.00</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-sale-row" aria-label="Quitar medicamento">
                                        <i class="bi bi-trash" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="small text-muted mb-0">El precio final se valida en el servidor. No se permiten productos repetidos ni cantidades superiores al stock.</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="text-muted small">Total estimado</div>
                <div class="h2 mb-0 fw-bold" id="saleTotal">Q 0.00</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button class="btn btn-success" type="submit" @disabled($products->isEmpty())>
                    <i class="bi bi-check2-circle me-1" aria-hidden="true"></i> Confirmar venta
                </button>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
(function () {
    const body = document.getElementById('saleItems');
    const addButton = document.getElementById('addSaleItem');
    const totalElement = document.getElementById('saleTotal');
    const money = value => 'Q ' + value.toLocaleString('es-GT', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    function reindexRows() {
        body.querySelectorAll('.sale-row').forEach((row, index) => {
            row.querySelector('.sale-product').name = `items[${index}][product_id]`;
            row.querySelector('.sale-quantity').name = `items[${index}][quantity]`;
        });
    }

    function recalculate() {
        let total = 0;
        body.querySelectorAll('.sale-row').forEach(row => {
            const select = row.querySelector('.sale-product');
            const selected = select.options[select.selectedIndex];
            const price = Number(selected?.dataset.price || 0);
            const available = Number(selected?.dataset.stock || 0);
            const quantity = Number(row.querySelector('.sale-quantity').value || 0);
            const subtotal = price * quantity;
            total += subtotal;
            row.querySelector('.sale-unit-price').textContent = money(price);
            row.querySelector('.sale-available').textContent = selected?.value ? available + ' uds.' : '—';
            row.querySelector('.sale-subtotal').textContent = money(subtotal);
            row.querySelector('.sale-quantity').setCustomValidity(
                selected?.value && quantity > available ? 'La cantidad supera las existencias disponibles.' : ''
            );
        });
        totalElement.textContent = money(total);
    }

    addButton?.addEventListener('click', () => {
        const firstRow = body.querySelector('.sale-row');
        if (!firstRow || body.querySelectorAll('.sale-row').length >= 50) return;
        const row = firstRow.cloneNode(true);
        row.querySelector('.sale-product').value = '';
        row.querySelector('.sale-quantity').value = 1;
        row.querySelector('.sale-quantity').setCustomValidity('');
        body.appendChild(row);
        reindexRows();
        recalculate();
    });

    body.addEventListener('click', event => {
        if (!event.target.closest('.remove-sale-row')) return;
        const rows = body.querySelectorAll('.sale-row');
        const row = event.target.closest('.sale-row');
        if (rows.length === 1) {
            row.querySelector('.sale-product').value = '';
            row.querySelector('.sale-quantity').value = 1;
        } else {
            row.remove();
        }
        reindexRows();
        recalculate();
    });

    body.addEventListener('change', recalculate);
    body.addEventListener('input', recalculate);
    recalculate();
})();
</script>
@endpush
@endsection
