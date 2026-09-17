<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\TrackingEvent;
use App\Services\Saweria\OrderRecorder;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $state = $request->query('status');
        $search = trim((string) $request->query('cari', ''));

        $orders = Order::query()
            ->when($state && $state !== 'semua', fn ($q) => $q->where('state', $state))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('saweria_id', 'like', "%{$search}%")
                        ->orWhere('donator_name', 'like', "%{$search}%")
                        ->orWhere('donator_email', 'like', "%{$search}%")
                        ->orWhere('game_name', 'like', "%{$search}%")
                        ->orWhere('product_name', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.orders', [
            'orders' => $orders,
            'state' => $state ?: 'semua',
            'search' => $search,
            'counts' => Order::selectRaw('state, COUNT(*) as total')
                ->groupBy('state')->pluck('total', 'state'),
            'total' => Order::count(),
        ]);
    }

    public function show(string $saweriaId)
    {
        $order = Order::where('saweria_id', $saweriaId)->firstOrFail();

        // Jejak kunjungan yang menghasilkan pesanan ini, bila berhasil dihubungkan
        $journey = $order->session_id
            ? TrackingEvent::where('session_id', $order->session_id)
                ->orderBy('occurred_at')
                ->limit(100)
                ->get()
            : collect();

        return view('admin.order-detail', compact('order', 'journey'));
    }

    /**
     * Ambil ulang detail dan status pesanan dari Saweria.
     */
    public function refresh(string $saweriaId, OrderRecorder $recorder)
    {
        $order = Order::where('saweria_id', $saweriaId)->firstOrFail();

        $ok = $recorder->enrich($order);

        return redirect()
            ->route('admin.orders.show', $order->saweria_id)
            ->with('status', $ok
                ? 'Data pesanan diperbarui dari Saweria.'
                : 'Saweria belum bisa dihubungi atau belum mengenali pesanan ini.');
    }
}
