<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MonthlyDigitalGiftRequest;
use App\Models\MonthlyDigitalGift;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MonthlyDigitalGiftController extends Controller
{
    public function edit(): View
    {
        $gift = MonthlyDigitalGift::orderByDesc('month')->first();

        return view('admin.monthly-digital-gift.edit', [
            'gift' => $gift,
        ]);
    }

    public function storeOrUpdate(MonthlyDigitalGiftRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $gift = MonthlyDigitalGift::updateOrCreate(
            ['month' => $data['month']],
            $data
        );

        return redirect()
            ->route('admin.monthly-digital-gift.edit')
            ->with('status', sprintf('%s Monthly Digital Gift saved.', $gift->month_label));
    }
}
