<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\JenisProduk;
use App\Models\Produk;
use App\Models\ProdukGambar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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
            'foto_tambahan' => 'nullable|array|max:4',
            'foto_tambahan.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'hapus_foto_tambahan' => 'nullable|array',
            'hapus_foto_tambahan.*' => 'integer',
        ];
    }

    private function simpanFoto(Request $request): string
    {
        return $this->simpanFile($request->file('foto'));
    }

    private function simpanFile($file): string
    {
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
        if ($request->filled('Harga')) {
            $request->merge(['Harga' => (int) preg_replace('/[^0-9]/', '', (string) $request->input('Harga'))]);
        }

        $data = $request->validate($this->rules(true));

        $produk = new Produk(collect($data)->only(['NamaProduk', 'JenisID', 'Deskripsi', 'Harga', 'Kondisi'])->all());
        $produk->Gambar = $this->simpanFoto($request);
        $produk->SellerID = Auth::id();   // dari sesi, bukan dari request
        $produk->Status = 'Tersedia';
        $produk->Stok = 1;
        $produk->save();

        if ($request->hasFile('foto_tambahan')) {
            foreach ($request->file('foto_tambahan') as $idx => $fileTambahan) {
                if ($fileTambahan) {
                    $namaFile = $this->simpanFile($fileTambahan);
                    ProdukGambar::create([
                        'ProdukID' => $produk->ProdukID,
                        'PathGambar' => $namaFile,
                        'Urutan' => $idx + 1,
                    ]);
                }
            }
        }

        return redirect()->route('seller.produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $produk = $this->milik($id);
        abort_unless($produk->Status === 'Tersedia', 403, 'Produk yang sedang dipesan/terjual tidak dapat diubah.');

        $produk->load('gambarTambahan');
        $categories = JenisProduk::aktif()->orderBy('NamaJenis')->get();
        return view('seller.produk.edit', ['produk' => $produk, 'categories' => $categories, 'kondisi' => Produk::KONDISI]);
    }

    public function update(Request $request, $id)
    {
        $produk = $this->milik($id);
        abort_unless($produk->Status === 'Tersedia', 403, 'Produk yang sedang dipesan/terjual tidak dapat diubah.');

        if ($request->filled('Harga')) {
            $request->merge(['Harga' => (int) preg_replace('/[^0-9]/', '', (string) $request->input('Harga'))]);
        }

        $data = $request->validate($this->rules(false));
        $produk->fill(collect($data)->only(['NamaProduk', 'JenisID', 'Deskripsi', 'Harga', 'Kondisi'])->all());
        if ($request->hasFile('foto')) {
            $fotoLama = $produk->Gambar;
            $produk->Gambar = $this->simpanFoto($request);

            if ($fotoLama && $fotoLama !== 'default.jpg' && !str_starts_with($fotoLama, 'http')) {
                $path = public_path('upload/' . $fotoLama);
                if (File::exists($path)) {
                    @File::delete($path);
                }
            }
        }
        $produk->save();

        // Hapus foto tambahan yang dicentang untuk dihapus
        if ($request->filled('hapus_foto_tambahan')) {
            $hapusIds = (array) $request->input('hapus_foto_tambahan');
            $gambarDihapus = ProdukGambar::where('ProdukID', $produk->ProdukID)
                ->whereIn('GambarID', $hapusIds)
                ->get();

            foreach ($gambarDihapus as $g) {
                if ($g->PathGambar && $g->PathGambar !== 'default.jpg' && !str_starts_with($g->PathGambar, 'http')) {
                    $path = public_path('upload/' . $g->PathGambar);
                    if (File::exists($path)) {
                        @File::delete($path);
                    }
                }
                $g->delete();
            }
        }

        // Simpan foto tambahan baru
        if ($request->hasFile('foto_tambahan')) {
            $urutan = (int) $produk->gambarTambahan()->max('Urutan');
            foreach ($request->file('foto_tambahan') as $fileTambahan) {
                if ($fileTambahan) {
                    $urutan++;
                    $namaFile = $this->simpanFile($fileTambahan);
                    ProdukGambar::create([
                        'ProdukID' => $produk->ProdukID,
                        'PathGambar' => $namaFile,
                        'Urutan' => $urutan,
                    ]);
                }
            }
        }

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
            $foto = $produk->Gambar;
            if ($foto && $foto !== 'default.jpg' && !str_starts_with($foto, 'http')) {
                $path = public_path('upload/' . $foto);
                if (File::exists($path)) {
                    @File::delete($path);
                }
            }

            // Hapus seluruh foto tambahan dari storage & DB
            foreach ($produk->gambarTambahan as $gt) {
                if ($gt->PathGambar && $gt->PathGambar !== 'default.jpg' && !str_starts_with($gt->PathGambar, 'http')) {
                    $path = public_path('upload/' . $gt->PathGambar);
                    if (File::exists($path)) {
                        @File::delete($path);
                    }
                }
                $gt->delete();
            }

            $produk->delete();
        }

        return back()->with('success', 'Produk ditarik dari katalog.');
    }
}