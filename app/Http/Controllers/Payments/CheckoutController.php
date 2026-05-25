<?php
namespace App\Http\Controllers\Payments;
use App\Http\Controllers\Controller;
use App\Models\CreditPackage;
use App\Services\CreditService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
class CheckoutController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly CreditService  $creditService
    ) {}
    public function index(Request $request): View
    {
        $packages = CreditPackage::where("is_active", true)
            ->orderBy("sort_order")
            ->get();
        $balance = $this->creditService->spendableBalance($request->user());
        return view("pages.credits.index", compact("packages", "balance"));
    }
    public function checkout(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            "package_id" => ["required", "uuid", "exists:credit_packages,id"],
        ]);
        $package = \App\Models\CreditPackage::findOrFail($validated["package_id"]);
        $url = $this->paymentService->createCheckoutSession($request->user(), $package);
        return redirect()->away($url);
    }
    public function createIntent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            "package_id" => ["required", "uuid", "exists:credit_packages,id"],
        ]);
        $package = CreditPackage::findOrFail($validated["package_id"]);
        if (!$package->is_active) {
            return response()->json(["error" => "Package not available"], 422);
        }
        $intent = $this->paymentService->createIntent($request->user(), $package);
        return response()->json($intent);
    }
}
