<?php

namespace App\Http\Controllers;

use App\Models\Percakapan;
use App\Models\Pesan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    private function sebagaiSeller(): bool
    {
        return request()->routeIs('seller.*');
    }

    private function saya()
    {
        return Auth::guard($this->sebagaiSeller() ? 'seller' : 'web')->user();
    }

    private function tampilan(): array
    {
        $seller = $this->sebagaiSeller();

        return [
            'layout' => $seller ? 'layouts.seller' : 'layouts.app',
            'section' => $seller ? 'seller_content' : 'content',
            'rp' => $seller ? 'seller.chat' : 'pelanggan.chat',
            'sebagaiSeller' => $seller,
            'uid' => (int) $this->saya()->UserID,
        ];
    }

    /** Hanya peserta percakapan yang boleh membuka (selain itu 404). */
    private function milik($id): Percakapan
    {
        $kolom = $this->sebagaiSeller() ? 'SellerID' : 'PembeliID';

        return Percakapan::where('PercakapanID', $id)
            ->where($kolom, (int) $this->saya()->UserID)
            ->firstOrFail();
    }

    private function tandaiDibaca(Percakapan $c): void
    {
        Pesan::where('PercakapanID', $c->PercakapanID)
            ->where('PengirimID', '!=', (int) $this->saya()->UserID)
            ->whereNull('DibacaPada')
            ->update(['DibacaPada' => now()]);
    }

    /** Pembeli menekan "Tanya Penjual" di halaman produk. */
    public function mulai($produkId)
    {
        $produk = Produk::where('Status', '!=', 'Disembunyikan')->findOrFail($produkId);
        $uid = (int) Auth::guard('web')->id();

        if (!$produk->SellerID || (int) $produk->SellerID === $uid) {
            return back()->with('error', 'Anda tidak dapat mengirim pesan ke toko Anda sendiri.');
        }

        $c = Percakapan::firstOrCreate([
            'PembeliID' => $uid,
            'SellerID' => (int) $produk->SellerID,
        ]);

        return redirect()->route('pelanggan.chat.show', [$c->PercakapanID, 'produk' => $produk->ProdukID]);
    }

    public function index()
    {
        $t = $this->tampilan();
        $kolom = $t['sebagaiSeller'] ? 'SellerID' : 'PembeliID';

        $daftar = Percakapan::with(['pembeli:UserID,Nama', 'seller:UserID,Nama', 'pesanTerakhir'])
            ->withCount(['pesan as belum_dibaca' => fn ($q) => $q
                ->whereNull('DibacaPada')
                ->where('PengirimID', '!=', $t['uid'])])
            ->where($kolom, $t['uid'])
            ->whereHas('pesan')
            ->orderByDesc('TerakhirPada')
            ->paginate(20);

        return view('chat.index', array_merge($t, compact('daftar')));
    }

    public function show($id)
    {
        $t = $this->tampilan();
        $c = $this->milik($id)->load(['pembeli:UserID,Nama', 'seller:UserID,Nama']);
        $this->tandaiDibaca($c);

        $pesan = Pesan::with('produk:ProdukID,NamaProduk')
            ->where('PercakapanID', $c->PercakapanID)
            ->orderByDesc('PesanID')
            ->limit(200)
            ->get()
            ->reverse()
            ->values();

        $lawan = $t['sebagaiSeller'] ? $c->pembeli : $c->seller;

        // Produk yang sedang ditanyakan (dari tombol di halaman detail produk)
        $produkRef = null;
        if (request()->filled('produk')) {
            $produkRef = Produk::where('SellerID', $c->SellerID)
                ->where('Status', '!=', 'Disembunyikan')
                ->find((int) request('produk'));
        }

        return view('chat.show', array_merge($t, compact('c', 'pesan', 'lawan', 'produkRef')));
    }

    public function kirim(Request $request, $id)
    {
        $c = $this->milik($id);

        $data = $request->validate([
            'isi' => 'required|string|max:1000',
            'produk_id' => 'nullable|integer',
        ], [
            'isi.required' => 'Pesan tidak boleh kosong.',
            'isi.max' => 'Pesan maksimal 1000 karakter.',
        ]);

        $produkId = null;
        if (!empty($data['produk_id'])) {
            $produkId = Produk::where('SellerID', $c->SellerID)
                ->where('ProdukID', (int) $data['produk_id'])
                ->value('ProdukID');
        }

        Pesan::create([
            'PercakapanID' => $c->PercakapanID,
            'PengirimID' => (int) $this->saya()->UserID,
            'ProdukID' => $produkId,
            'Isi' => trim($data['isi']),
            'DibuatPada' => now(),
        ]);

        $c->TerakhirPada = now();
        $c->save();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route($this->tampilan()['rp'] . '.show', $c->PercakapanID);
    }

    /** Dipanggil JavaScript tiap beberapa detik: ambil pesan yang lebih baru dari ?setelah=ID */
    public function pesan(Request $request, $id)
    {
        $c = $this->milik($id);
        $setelah = (int) $request->query('setelah', 0);

        $baru = Pesan::with('produk:ProdukID,NamaProduk')
            ->where('PercakapanID', $c->PercakapanID)
            ->where('PesanID', '>', $setelah)
            ->orderBy('PesanID')
            ->get();

        $this->tandaiDibaca($c);

        return response()->json([
            'html' => view('chat._pesan', ['pesan' => $baru, 'uid' => (int) $this->saya()->UserID])->render(),
            'terakhir' => (int) ($baru->max('PesanID') ?? $setelah),
            'jumlah' => $baru->count(),
        ]);
    }
}

