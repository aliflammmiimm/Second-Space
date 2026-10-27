<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\JenisProduk;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProdukController extends Controller
{
    private function rules(bool $fotoWajib): array
    {
        return [
            'NamaProduk' => 'required|string|max:150',
            'JenisID' => ['required', Rule::exists('jenis_produk', 'JenisID')->where('Aktif', 1)],
            'Deskripsi' => 'required|string|max:3000',
            'Harga' => 'required|integer|min:1|max:1000000000',
            'Kondisi' => ['required', Rule::in(array_keys(Produk::KONDISI))],
            'foto' => ($fotoWajib ? 'required' : 'nullable') . '|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    private function simpanFoto(Request $request): string
    {
        $file = $request->file('foto');
        $nama = time() . '_' . Str::random(10) . '.' . $file->guessExtension();
        $file->move(public_path('upload'), $nama);
        return $nama;
    }

    // Selalu query lewat relasi milik user -> produk Seller lain = 404
    private function milik($id): Produk
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->produk()->where('ProdukID', $id)->firstOrFail();
    }

    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $semua = $user->produk();
        $ringkasan = [
            'tersedia' => (clone $semua)->where('Status', 'Tersedia')->count(),
            'dipesan' => (clone $semua)->where('Status', 'Dipesan')->count(),
            'terjual' => (clone $semua)->where('Status', 'Terjual')->count(),
            'disembunyikan' => (clone $semua)->where('Status', 'Disembunyikan')->count(),
        ];
        $produk = $user->produk()->with('jenisProduk')->latest('ProdukID')->paginate(10);

        $selesai = \App\Models\PesananDetail::where('SellerID', Auth::id())
            ->whereHas('pesanan', fn ($q) => $q->where('StatusPesanan', 'Selesai'));

        $penjualanKotor = (clone $selesai)->sum('SubTotal');
        $totalKomisi = (clone $selesai)->sum('Komisi');
        $pendapatan = (clone $selesai)->sum('PendapatanSeller'); // bersih, setelah komisi

        $pesananAktif = \App\Models\PesananDetail::where('SellerID', Auth::id())
            ->whereHas('pesanan', fn ($q) => $q->whereNotIn('StatusPesanan', ['Selesai', 'Dibatalkan', 'Kedaluwarsa']))
            ->count();

        return view('seller.produk.index', compact('produk', 'ringkasan', 'pendapatan', 'penjualanKotor', 'totalKomisi', 'pesananAktif'));
    }

    public function create()
    {
        $categories = JenisProduk::aktif()->orderBy('NamaJenis')->get();
        return view('seller.produk.create', ['categories' => $categories, 'kondisi' => Produk::KONDISI]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules(true));

        $produk = new Produk(collect($data)->only(['NamaProduk', 'JenisID', 'Deskripsi', 'Harga', 'Kondisi'])->all());
        $produk->Gambar = $this->simpanFoto($request);
        $produk->SellerID = Auth::id();   // dari sesi, bukan dari request
        $produk->Status = 'Tersedia';
        $produk->Stok = 1;
        $produk->save();

        return redirect()->route('seller.produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $produk = $this->milik($id);
        abort_unless($produk->Status === 'Tersedia', 403, 'Produk yang sedang dipesan/terjual tidak dapat diubah.');

        $categories = JenisProduk::aktif()->orderBy('NamaJenis')->get();
        return view('seller.produk.edit', ['produk' => $produk, 'categories' => $categories, 'kondisi' => Produk::KONDISI]);
    }

    public function update(Request $request, $id)
    {
        $produk = $this->milik($id);
        abort_unless($produk->Status === 'Tersedia', 403, 'Produk yang sedang dipesan/terjual tidak dapat diubah.');

        $data = $request->validate($this->rules(false));
        $produk->fill(collect($data)->only(['NamaProduk', 'JenisID', 'Deskripsi', 'Harga', 'Kondisi'])->all());
        if ($request->hasFile('foto')) {
            $produk->Gambar = $this->simpanFoto($request);
        }
        $produk->save();

        return redirect()->route('seller.produk.index')->with('success', 'Produk diperbarui.');
    }

    // "Tarik produk": hapus jika belum pernah dipesan, selain itu sembunyikan (riwayat aman)
    public function tarik($id)
    {
        $produk = $this->milik($id);
        abort_unless($produk->Status === 'Tersedia', 403, 'Hanya produk yang belum terjual yang bisa ditarik.');

        if ($produk->pesananDetail()->exists()) {
            $produk->Status = 'Disembunyikan';
            $produk->save();
        } else {
            $produk->delete();
        }

        return back()->with('success', 'Produk ditarik dari katalog.');
    }
}