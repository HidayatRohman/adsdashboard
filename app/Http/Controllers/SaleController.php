<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::query()->with(['marketing','product']);

        if ($request->filled('search')) {
            $query->where('nama_lengkap_mitra', 'like', '%'.$request->search.'%');
        }

        $sales = $query->orderBy('created_at','desc')->paginate(10)->withQueryString();

        $products = Product::orderBy('name')->get();

        return Inertia::render('sales/Index', [
            'sales' => $sales,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal_transfer' => 'nullable|date',
            'tanggal_lead_masuk' => 'nullable|date',
            'periode_lead_bulan' => 'nullable|integer',
            'nama_lengkap_mitra' => 'nullable|string',
            'no_wa' => 'nullable|string',
            'umur' => 'nullable|integer',
            'pekerjaan' => 'nullable|string',
            'product_id' => 'nullable|exists:products,id',
            'lead_awal' => 'nullable|string',
            'sumber' => 'nullable|string',
            'kabupaten' => 'nullable|string',
            'provinsi' => 'nullable|string',
            'status' => 'nullable|string',
            'nominal_masuk' => 'nullable|integer',
            'harga_paket' => 'nullable|integer',
        ]);

        $data['marketing_id'] = Auth::id();

        Sale::create($data);

        return redirect()->back()->with('success', 'Sale created.');
    }

    public function update(Request $request, Sale $sale)
    {
        $data = $request->validate([
            'tanggal_transfer' => 'nullable|date',
            'tanggal_lead_masuk' => 'nullable|date',
            'periode_lead_bulan' => 'nullable|integer',
            'nama_lengkap_mitra' => 'nullable|string',
            'no_wa' => 'nullable|string',
            'umur' => 'nullable|integer',
            'pekerjaan' => 'nullable|string',
            'product_id' => 'nullable|exists:products,id',
            'lead_awal' => 'nullable|string',
            'sumber' => 'nullable|string',
            'kabupaten' => 'nullable|string',
            'provinsi' => 'nullable|string',
            'status' => 'nullable|string',
            'nominal_masuk' => 'nullable|integer',
            'harga_paket' => 'nullable|integer',
        ]);

        $sale->update($data);

        return redirect()->back()->with('success', 'Sale updated.');
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();
        return redirect()->back()->with('success', 'Sale deleted.');
    }
}
