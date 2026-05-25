<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\PaymentOrder;
class PaymentController extends Controller
{
    public function index()
    {
        $payments = PaymentOrder::with(['user', 'creditPackage'])
            ->latest()
            ->paginate(30);
        return view('pages.admin.payments.index', compact('payments'));
    }
}
