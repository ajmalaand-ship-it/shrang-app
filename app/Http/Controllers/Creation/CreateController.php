<?php
namespace App\Http\Controllers\Creation;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
class CreateController extends Controller
{
    public function index(): View
    {
        return view("pages.create.index");
    }
}
