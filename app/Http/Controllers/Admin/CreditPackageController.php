<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\CreditPackage;
use Illuminate\Http\Request;
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
            'name'       => ['required', 'string', 'max:100'],
            'credits'    => ['required', 'integer', 'min:1'],
            'price_cents'=> ['required', 'integer', 'min:1'],
            'currency'   => ['required', 'string', 'max:3'],
            'sort_order' => ['required', 'integer'],
        ]);
        $validated['is_active'] = true;
        CreditPackage::create($validated);
        return redirect()->route('admin.packages.index')->with('success', 'Package created.');
    }
    public function update(Request $request, CreditPackage $package)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'credits'    => ['required', 'integer', 'min:1'],
            'price_cents'=> ['required', 'integer', 'min:1'],
            'sort_order' => ['required', 'integer'],
        ]);
        $package->update($validated);
        return redirect()->route('admin.packages.index')->with('success', 'Package updated.');
    }
    public function toggle(CreditPackage $package)
    {
        $package->update(['is_active' => !$package->is_active]);
        return redirect()->route('admin.packages.index')
            ->with('success', $package->is_active ? 'Package activated.' : 'Package deactivated.');
    }
}
