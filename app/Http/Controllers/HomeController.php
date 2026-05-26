<?php
namespace App\Http\Controllers;
use Illuminate\View\View;
class HomeController extends Controller
{
    public function index(): View
    {
        $locale = app()->getLocale();
        $view = match($locale) {
            'ps' => 'welcome-ps',
            'fa' => 'welcome-fa',
            'ur' => 'welcome-ur',
            'ar' => 'welcome-ar',
            'hi' => 'welcome-hi',
            default => 'welcome',
        };
        if (!view()->exists($view)) {
            $view = 'welcome';
        }
        return view($view);
    }
}
