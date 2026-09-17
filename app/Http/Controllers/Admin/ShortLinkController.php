<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShortLink;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShortLinkController extends Controller
{
    public function index(Request $request)
    {
        $links = ShortLink::orderByDesc('created_at')->paginate(30)->withQueryString();

        return view('admin.shortlinks', [
            'links' => $links,
            'templates' => config('shortlinks.templates'),
            'totalClicks' => (int) ShortLink::sum('clicks'),
            'humanClicks' => (int) ShortLink::sum('human_clicks'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $code = $data['code'] ?? null;
        unset($data['code']);

        $link = ShortLink::create($data + [
            'code' => $code ?: ShortLink::generateCode(),
        ]);

        // URL dikirim terpisah agar halaman bisa menampilkannya dengan tombol salin,
        // bukan sekadar teks di dalam kalimat.
        return redirect()->route('admin.shortlinks')
            ->with('created_url', $link->short_url);
    }

    public function update(Request $request, ShortLink $shortLink)
    {
        $data = $this->validated($request, $shortLink);

        // Kode dibiarkan berubah hanya bila diisi; mengosongkannya berarti
        // pengelola tidak bermaksud menggantinya.
        if (empty($data['code'])) {
            unset($data['code']);
        }

        $shortLink->update($data);

        return redirect()->route('admin.shortlinks')->with('status', 'Tautan diperbarui.');
    }

    public function toggle(ShortLink $shortLink)
    {
        $shortLink->update(['active' => ! $shortLink->active]);

        return redirect()->route('admin.shortlinks')
            ->with('status', $shortLink->active ? 'Tautan diaktifkan.' : 'Tautan dinonaktifkan.');
    }

    public function destroy(ShortLink $shortLink)
    {
        $code = $shortLink->code;
        $shortLink->delete();

        return redirect()->route('admin.shortlinks')
            ->with('status', "Tautan /s/{$code} dihapus. Tautan yang sudah tersebar tidak akan berfungsi lagi.");
    }

    protected function validated(Request $request, ?ShortLink $existing = null): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:120'],
            'target' => ['required', 'url', 'max:2000'],
            'template' => ['nullable', 'string', Rule::in(array_keys(config('shortlinks.templates')))],
            // Huruf, angka, dan tanda hubung saja: kode harus mudah didikte
            // lewat telepon dan aman dipakai di dalam URL.
            'code' => [
                'nullable', 'string', 'max:40', 'regex:/^[A-Za-z0-9\-]+$/',
                Rule::unique('short_links', 'code')->ignore($existing?->id),
            ],
            'utm_source' => ['nullable', 'string', 'max:80'],
            'utm_medium' => ['nullable', 'string', 'max:80'],
            'utm_campaign' => ['nullable', 'string', 'max:80'],
            'utm_content' => ['nullable', 'string', 'max:80'],
            'utm_term' => ['nullable', 'string', 'max:80'],
            'expires_at' => ['nullable', 'date'],
            'active' => ['nullable', 'boolean'],
        ], [
            'target.url' => 'Tujuan harus berupa URL lengkap, termasuk https://',
            'code.regex' => 'Kode hanya boleh berisi huruf, angka, dan tanda hubung.',
            'code.unique' => 'Kode itu sudah dipakai tautan lain.',
        ]) + ['active' => $request->boolean('active', true)];
    }
}
