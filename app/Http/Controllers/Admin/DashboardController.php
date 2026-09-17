<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Analytics\FunnelReport;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $days = (int) $request->query('hari', 30);
        $days = in_array($days, [7, 30, 90], true) ? $days : 30;

        $report = FunnelReport::forDays($days);

        return view('admin.dashboard', [
            'days' => $days,
            'summary' => $report->summary(),
            'funnel' => $report->funnel(),
            'sources' => $report->sources(),
            'products' => $report->products(),
            'daily' => $report->daily(),
            'recentOrders' => Order::orderByDesc('created_at')->limit(8)->get(),
        ]);
    }
}
