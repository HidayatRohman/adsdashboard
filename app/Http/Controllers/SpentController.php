<?php

namespace App\Http\Controllers;

use App\Models\Spent;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class SpentController extends Controller
{
    public function index()
    {
        $spents = Spent::orderBy('tanggal', 'desc')->paginate(10);
        return Inertia::render('spent/Index', [
            'spents' => $spents,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'nominal' => 'required|integer|min:0',
        ]);

        $data['user_id'] = Auth::id();

        $spent = Spent::create($data);

        return redirect()->back()->with('success', 'Spent created');
    }

    public function update(Request $request, Spent $spent)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'nominal' => 'required|integer|min:0',
        ]);

        $spent->update($data);

        return redirect()->back()->with('success', 'Spent updated');
    }

    public function destroy(Spent $spent)
    {
        $spent->delete();

        return redirect()->back()->with('success', 'Spent deleted');
    }
}
