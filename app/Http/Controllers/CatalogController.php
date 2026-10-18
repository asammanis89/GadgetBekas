<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    private const SORTS = [
        'terbaru' => ['created_at', 'desc'],
        'termurah' => ['price_sell', 'asc'],
        'termahal' => ['price_sell', 'desc'],
        'baterai' => ['battery_health', 'desc'],
    ];

    public function index(Request $request)
    {
        $query = Product::query()->with(['brand', 'photos'])->listed();

        $q = $request->string('q')->trim()->toString();
        if ($q !== '') {
            $like = '%'.addcslashes($q, '%_\\').'%';
            $query->where(function ($w) use ($like) {
                $w->where('model', 'like', $like)
                    ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', $like));
            });
        }

        if ($brand = $request->string('merek')->toString()) {
            $query->whereHas('brand', fn ($b) => $b->where('slug', $brand));
        }

        if (in_array($grade = $request->string('grade')->toString(), array_keys(Product::GRADES), true)) {
            $query->where('grade', $grade);
        }

        if ($max = (int) $request->input('maks')) {
            $query->where('price_sell', '<=', $max);
        }

        $sort = array_key_exists((string) $request->input('urut'), self::SORTS) ? (string) $request->input('urut') : 'terbaru';
        [$column, $direction] = self::SORTS[$sort];
        $query->orderBy($column, $direction)->orderByDesc('id');

        return view('catalog.index', [
            'products' => $query->paginate(12)->withQueryString(),
            'brands' => Brand::whereHas('products', fn ($p) => $p->listed())->orderBy('name')->get(),
            'sorts' => ['terbaru' => 'Terbaru', 'termurah' => 'Harga termurah', 'termahal' => 'Harga termahal', 'baterai' => 'Baterai terbaik'],
            'sort' => $sort,
            'filters' => ['q' => $q, 'merek' => $brand ?: '', 'grade' => $grade ?: '', 'maks' => $max ?: ''],
        ]);
    }

    public function show(Product $product)
    {
        $product->load(['brand', 'photos']);

        $related = Product::with(['brand', 'photos'])->listed()
            ->where('brand_id', $product->brand_id)->whereKeyNot($product->id)
            ->latest()->take(4)->get();

        return view('catalog.show', ['product' => $product, 'related' => $related]);
    }
}
