<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CreditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
class UserController extends Controller
{
    public function __construct(private readonly CreditService $creditService) {}
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->search, fn($q) => $q->where("email", "like", "%" . $request->search . "%")
                ->orWhere("name", "like", "%" . $request->search . "%"))
            ->orderByDesc("created_at")
            ->paginate(30);
        return view("pages.admin.users.index", compact("users"));
    }
    public function show(User $user): View
    {
        $balance = $this->creditService->spendableBalance($user);
        return view("pages.admin.users.show", compact("user", "balance"));
    }
    public function toggleBan(User $user): RedirectResponse
    {
        $user->update(["is_active" => !$user->is_active]);
        return redirect()->route("admin.users.index")
            ->with("success", "User " . ($user->is_active ? "unbanned" : "banned") . ".");
    }
    public function adjustCredits(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            "amount" => ["required", "integer", "between:-10000,10000"],
            "reason" => ["required", "string", "max:200"],
        ]);
        $this->creditService->manualAdjust(
            $user,
            $validated["amount"],
            $validated["reason"],
            $request->user()->id
        );
        return redirect()->route("admin.users.show", $user)
            ->with("success", "Credits adjusted.");
    }
}
