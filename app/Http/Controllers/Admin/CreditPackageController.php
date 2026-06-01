<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreditPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditPackageController extends Controller
{
    public function index()
    {
        $packages = CreditPackage::orderBy('sort_order')->get();
        return view('pages.admin.packages.index', compact('packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:100'],
            'credits'         => ['required', 'integer', 'min:1'],
            'price_dollars'   => ['required', 'numeric', 'min:0.01'],
            'currency'        => ['required', 'string', 'max:3'],
            'sort_order'      => ['required', 'integer'],
            'stripe_price_id' => ['nullable', 'string', 'max:100'],
        ]);

        CreditPackage::create([
            'name'            => $validated['name'],
            'credits'         => $validated['credits'],
            'price_cents'     => (int) round($validated['price_dollars'] * 100),
            'currency'        => $validated['currency'],
            'sort_order'      => $validated['sort_order'],
            'stripe_price_id' => $validated['stripe_price_id'] ?? null,
            'is_active'       => true,
        ]);

        return redirect()->route('admin.packages.index')->with('success', 'Package created.');
    }

    public function update(Request $request, CreditPackage $package)
    {
        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:100'],
            'credits'         => ['required', 'integer', 'min:1'],
            'price_dollars'   => ['required', 'numeric', 'min:0.01'],
            'sort_order'      => ['required', 'integer'],
            'stripe_price_id' => ['nullable', 'string', 'max:100'],
        ]);

        $package->update([
            'name'            => $validated['name'],
            'credits'         => $validated['credits'],
            'price_cents'     => (int) round($validated['price_dollars'] * 100),
            'sort_order'      => $validated['sort_order'],
            'stripe_price_id' => $validated['stripe_price_id'] ?? null,
        ]);

        return redirect()->route('admin.packages.index')->with('success', 'Package updated.');
    }

    public function toggle(CreditPackage $package)
    {
        $package->update(['is_active' => !$package->is_active]);
        return redirect()->route('admin.packages.index')
            ->with('success', $package->is_active ? 'Package activated.' : 'Package deactivated.');
    }

    public function destroy(CreditPackage $package)
    {
        $hasOrders = DB::table('payment_orders')
            ->where('credit_package_id', $package->id)
            ->exists();

        if ($hasOrders) {
            return redirect()->route('admin.packages.index')
                ->withErrors(['delete' => 'Cannot delete a package that has payment history. Disable it instead.']);
        }

        $package->delete();
        return redirect()->route('admin.packages.index')->with('success', 'Package deleted.');
    }
}
