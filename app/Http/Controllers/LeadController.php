<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::query()->with(['marketing','product']);

        if ($request->filled('search')) {
            $query->where('customer_name', 'like', '%'.$request->search.'%');
        }

        $leads = $query->orderBy('created_at','desc')->paginate(10)->withQueryString();

        $products = Product::orderBy('name')->get();

        return Inertia::render('leads/Index', [
            'leads' => $leads,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'customer_name' => 'nullable|string',
            'customer_phone' => 'nullable|string',
            'address' => 'nullable|string',
            'last_response' => 'nullable|string',
        ]);

        $data['marketing_id'] = Auth::id();

        Lead::create($data);

        return redirect()->back()->with('success', 'Lead created.');
    }

    public function update(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'customer_name' => 'nullable|string',
            'customer_phone' => 'nullable|string',
            'address' => 'nullable|string',
            'last_response' => 'nullable|string',
        ]);

        $lead->update($data);

        return redirect()->back()->with('success', 'Lead updated.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->back()->with('success', 'Lead deleted.');
    }
}
