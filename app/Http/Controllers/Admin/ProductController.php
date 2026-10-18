<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'photos'])->latest('id');

        $q = $request->string('q')->trim()->toString();
        if ($q !== '') {
            $like = '%'.addcslashes($q, '%_\\').'%';
            $query->where(fn ($w) => $w->where('model', 'like', $like)->orWhere('imei', 'like', $like)
                ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', $like)));
        }
        if (array_key_exists($status = $request->string('status')->toString(), Product::STATUSES)) {
            $query->where('status', $status);
        }

        return view('admin.products.index', [
            'products' => $query->paginate(15)->withQueryString(),
            'q' => $q,
            'status' => $status,
        ]);
    }

    public function create()
    {
        return view('admin.products.form', ['product' => new Product(['status' => Product::AVAILABLE, 'grade' => 'A', 'warranty_days' => 14]), 'brands' => Brand::orderBy('name')->get()]);
    }

    public function store(ProductRequest $request)
    {
        $data = $request->safe()->except('photos');
        $data['slug'] = $this->uniqueSlug($data);
        $data['price_cost'] = $data['price_cost'] ?? 0;
        $data['warranty_days'] = $data['warranty_days'] ?? 0;
        $data['sold_at'] = $data['status'] === Product::SOLD ? now() : null;

        $product = Product::create($data);
        $this->storePhotos($product, $request->file('photos', []));

        return redirect()->route('admin.products.index')->with('status', "{$product->title} ditambahkan.");
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', ['product' => $product->load('photos'), 'brands' => Brand::orderBy('name')->get()]);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->safe()->except('photos');
        $data['price_cost'] = $data['price_cost'] ?? 0;
        $data['warranty_days'] = $data['warranty_days'] ?? 0;
        $data['sold_at'] = $this->soldAt($product, $data['status']);

        $product->update($data);
        $this->storePhotos($product, $request->file('photos', []));

        return redirect()->route('admin.products.index')->with('status', "{$product->title} diperbarui.");
    }

    /** Ubah status cepat dari daftar: tersedia, dipesan, terjual. */
    public function status(Request $request, Product $product)
    {
        $status = $request->validate(['status' => ['required', Rule::in(array_keys(Product::STATUSES))]])['status'];
        $product->update(['status' => $status, 'sold_at' => $this->soldAt($product, $status)]);

        return back()->with('status', "{$product->title} sekarang berstatus ".Str::lower(Product::STATUSES[$status]).'.');
    }

    public function destroy(Product $product)
    {
        foreach ($product->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }
        $title = $product->title;
        $product->delete();

        return redirect()->route('admin.products.index')->with('status', "$title dihapus.");
    }

    private function soldAt(Product $product, string $status): mixed
    {
        if ($status !== Product::SOLD) {
            return null;
        }

        return $product->sold_at ?? now(); // jangan menimpa tanggal terjual yang sudah ada
    }

    private function uniqueSlug(array $data): string
    {
        $brand = Brand::find($data['brand_id']);
        $base = Str::slug(trim("{$brand->name} {$data['model']} {$data['storage_gb']}"));
        $slug = $base;
        for ($i = 2; Product::where('slug', $slug)->exists(); $i++) {
            $slug = "$base-$i";
        }

        return $slug;
    }

    /** @param array<int, \Illuminate\Http\UploadedFile> $files */
    private function storePhotos(Product $product, array $files): void
    {
        $position = (int) $product->photos()->max('position');
        foreach ($files as $file) {
            $product->photos()->create([
                'path' => $file->store("products/{$product->id}", 'public'),
                'position' => ++$position,
            ]);
        }
    }
}
