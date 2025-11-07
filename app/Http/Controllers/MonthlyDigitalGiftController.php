<?php

namespace App\Http\Controllers;

use App\Models\MonthlyDigitalGift;
use Illuminate\View\View;

class MonthlyDigitalGiftController extends Controller
{
    public function __invoke(): View
    {
        $gift = MonthlyDigitalGift::orderByDesc('month')->first();

        return view('monthly-digital-gift', [
            'gift' => $gift,
        ]);
    }
}
