<?php

namespace App\Http\Controllers;

use App\Services\Saweria\OrderTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

class OrderTrackingController extends Controller
{
    public function index()
    {
        return response()->view('pages.track-order', $this->viewData());
    }

    public function lookup(Request $request, OrderTrackingService $tracking)
    {
        $rawTrackId = $request->input('track_id');
        $trackId = is_string($rawTrackId) ? trim($rawTrackId) : '';
        $validator = Validator::make(
            ['track_id' => is_string($rawTrackId) ? $trackId : $rawTrackId],
            ['track_id' => ['required', 'string', 'max:36', 'uuid']],
            [
                'track_id.required' => 'Track ID wajib diisi.',
                'track_id.string' => 'Track ID tidak valid.',
                'track_id.max' => 'Track ID tidak valid.',
                'track_id.uuid' => 'Track ID harus berupa UUID yang valid.',
            ]
        );

        if ($validator->fails()) {
            return $this->result(
                $request,
                $this->viewData(null, $validator->errors()->first('track_id'), $trackId),
                422
            );
        }

        try {
            $order = $tracking->lookup($trackId);
        } catch (RuntimeException|\Illuminate\Http\Client\ConnectionException) {
            return $this->result(
                $request,
                $this->viewData(null, 'Status pesanan sedang tidak tersedia. Coba lagi sebentar.', $trackId),
                503
            );
        }

        if ($order === null) {
            return $this->result(
                $request,
                $this->viewData(null, 'Pesanan tidak ditemukan. Periksa kembali Track ID.', $trackId),
                404
            );
        }

        return $this->result($request, $this->viewData($order, null, $trackId));
    }

    protected function viewData(?array $order = null, ?string $error = null, string $trackId = ''): array
    {
        return compact('order', 'error', 'trackId');
    }

    protected function result(Request $request, array $data, int $status = 200)
    {
        $response = $request->expectsJson()
            ? response()->json($data, $status)
            : response()->view('pages.track-order', $data, $status);

        return $response->withHeaders([
            'Cache-Control' => 'private, no-store, max-age=0',
            'Pragma' => 'no-cache',
            'X-Robots-Tag' => 'noindex, nofollow',
        ]);
    }
}
