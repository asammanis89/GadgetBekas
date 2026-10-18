<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // akses dijaga middleware `auth` pada rute admin
    }

    public function rules(): array
    {
        return [
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'model' => ['required', 'string', 'max:80'],
            'storage_gb' => ['required', 'integer', 'min:1', 'max:4096'],
            'ram_gb' => ['nullable', 'integer', 'min:1', 'max:64'],
            'color' => ['nullable', 'string', 'max:40'],
            'grade' => ['required', Rule::in(array_keys(Product::GRADES))],
            'battery_health' => ['nullable', 'integer', 'between:1,100'],
            'price_sell' => ['required', 'integer', 'min:0', 'max:200000000'],
            'price_cost' => ['nullable', 'integer', 'min:0', 'max:200000000'],
            'status' => ['required', Rule::in(array_keys(Product::STATUSES))],
            'imei' => ['nullable', 'regex:/^\d{15}$/'],
            'accessories' => ['nullable', 'string', 'max:300'],
            'description' => ['nullable', 'string', 'max:2000'],
            'warranty_days' => ['nullable', 'integer', 'between:0,365'],
            'photos' => ['nullable', 'array', 'max:8'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    public function attributes(): array
    {
        return [
            'brand_id' => 'merek', 'model' => 'model', 'storage_gb' => 'penyimpanan', 'ram_gb' => 'RAM',
            'color' => 'warna', 'grade' => 'grade', 'battery_health' => 'kesehatan baterai',
            'price_sell' => 'harga jual', 'price_cost' => 'harga modal', 'status' => 'status',
            'imei' => 'IMEI', 'accessories' => 'kelengkapan', 'description' => 'deskripsi',
            'warranty_days' => 'garansi', 'photos' => 'foto', 'photos.*' => 'foto',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'integer' => ':attribute harus berupa angka bulat.',
            'max' => ':attribute terlalu besar atau terlalu panjang.',
            'min' => ':attribute terlalu kecil.',
            'between' => ':attribute di luar rentang yang diperbolehkan.',
            'exists' => ':attribute tidak ditemukan.',
            'in' => ':attribute tidak valid.',
            'imei.regex' => 'IMEI harus 15 digit angka.',
            'photos.*.image' => 'Berkas foto harus berupa gambar.',
            'photos.*.mimes' => 'Foto harus berformat JPG, PNG, atau WebP.',
            'photos.*.max' => 'Ukuran tiap foto maksimal 4 MB.',
        ];
    }
}
