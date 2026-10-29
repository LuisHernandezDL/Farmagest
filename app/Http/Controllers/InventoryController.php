<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventoryController extends Controller
{
    private const STOCK_STATUSES = ['low', 'out', 'available'];
    private const DIRECTIONS = ['in', 'out'];

    public function index(Request $request): View
    {
        $search = $this->cleanSearch($request->query('search'));
        $categoryId = max(0, (int) $request->query('category', 0));
        $status = in_array($request->query('status'), self::STOCK_STATUSES, true)
            ? $request->query('status')
            : '';

        $products = Product::query()
            ->with('category:id,name')
            ->when($search !== '', fn (Builder $query) => $this->searchProducts($query, $search))
            ->when($categoryId > 0, fn (Builder $query) => $query->where('category_id', $categoryId))
            ->when($status === 'low', fn (Builder $query) => $query->whereColumn('stock', '<=', 'min_stock'))
            ->when($status === 'out', fn (Builder $query) => $query->where('stock', 0))
            ->when($status === 'available', fn (Builder $query) => $query->whereColumn('stock', '>', 'min_stock'))
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        $summary = [
            'total' => Product::count(),
            'units' => (int) Product::sum('stock'),
            'low' => Product::whereColumn('stock', '<=', 'min_stock')->count(),
            'out' => Product::where('stock', 0)->count(),
        ];

        return view('inventory.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'summary' => $summary,
            'filters' => [
                'search' => $search,
                'category' => $categoryId,
                'status' => $status,
            ],
        ]);
    }

    public function movements(Request $request): View
    {
        $search = $this->cleanSearch($request->query('search'));
        $direction = in_array($request->query('direction'), self::DIRECTIONS, true)
            ? $request->query('direction')
            : '';
        $from = $this->parseDate($request->query('from'));
        $to = $this->parseDate($request->query('to'));

        // Si el rango llega invertido se corrige en lugar de devolver una lista vacía.
        if ($from && $to && $from > $to) {
            [$from, $to] = [$to, $from];
        }

        $productId = max(0, (int) $request->query('product', 0));
        $selectedProduct = $productId > 0
            ? Product::query()->select(['id', 'code', 'name', 'stock'])->find($productId)
            : null;

        $query = StockMovement::query()
            ->when($selectedProduct, fn (Builder $query) => $query->where('product_id', $selectedProduct->id))
            ->when($search !== '', fn (Builder $query) => $query->whereHas(
                'product',
                fn (Builder $product) => $this->searchProducts($product, $search)
            ))
            ->when($direction !== '', fn (Builder $query) => $query->where('direction', $direction))
            ->when($from, fn (Builder $query) => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn (Builder $query) => $query->whereDate('created_at', '<=', $to));

        // Totales de los resultados filtrados, calculados en una sola consulta.
        $totals = (clone $query)->toBase()
            ->selectRaw('COUNT(*) as movements')
            ->selectRaw("COALESCE(SUM(CASE WHEN direction = 'in' THEN quantity ELSE 0 END), 0) as units_in")
            ->selectRaw("COALESCE(SUM(CASE WHEN direction = 'out' THEN quantity ELSE 0 END), 0) as units_out")
            ->first();

        $movements = $query
            ->with('product:id,code,name')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('inventory.movements', [
            'movements' => $movements,
            'references' => $this->resolveReferences($movements->getCollection()),
            'selectedProduct' => $selectedProduct,
            'totals' => [
                'movements' => (int) $totals->movements,
                'in' => (int) $totals->units_in,
                'out' => (int) $totals->units_out,
            ],
            'hasAnyMovement' => StockMovement::query()->exists(),
            'filters' => [
                'search' => $search,
                'direction' => $direction,
                'from' => $from,
                'to' => $to,
            ],
        ]);
    }

    private function searchProducts(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            $query->where('code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%");
        });
    }

    private function cleanSearch(mixed $value): string
    {
        return is_string($value) ? mb_substr(trim($value), 0, 100) : '';
    }

    private function parseDate(mixed $value): ?string
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        $date = DateTime::createFromFormat('!Y-m-d', $value);

        return $date && $date->format('Y-m-d') === $value ? $value : null;
    }

    private function resolveReferences(Collection $movements): array
    {
        $sources = [
            'purchase_item' => ['table' => 'purchase_items', 'parent' => 'purchase_id', 'label' => 'Compra'],
            'sale_item' => ['table' => 'sale_items', 'parent' => 'sale_id', 'label' => 'Venta'],
        ];

        $references = [];

        foreach ($sources as $type => $source) {
            $ids = $movements->where('source_type', $type)->pluck('source_id')->unique()->values();

            if ($ids->isEmpty()) {
                continue;
            }

            DB::table($source['table'])
                ->whereIn('id', $ids)
                ->pluck($source['parent'], 'id')
                ->each(function ($parentId, $itemId) use (&$references, $type, $source) {
                    $references["{$type}:{$itemId}"] = "{$source['label']} #{$parentId}";
                });
        }

        return $references;
    }
}
