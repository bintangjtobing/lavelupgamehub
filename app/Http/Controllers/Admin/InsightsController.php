<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Google\InsightsReport;
use Illuminate\Http\Request;

class InsightsController extends Controller
{
    public function __invoke(Request $request, InsightsReport $insights)
    {
        $days = (int) $request->query('hari', 28);
        $days = in_array($days, [7, 28, 90], true) ? $days : 28;

        return view('admin.insights', [
            'days' => $days,
            'data' => $insights->build($days),
        ]);
    }
}
