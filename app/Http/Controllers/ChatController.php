<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Percakapan;
use App\Models\Pesan;
use App\Models\Produk;
use App\Models\TawaranHarga;
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
        $myUser = $this->saya();
        if (!$myUser) return;
        $myId = (int) $myUser->UserID;

        Pesan::where('PercakapanID', $c->PercakapanID)
            ->where('PengirimID', '!=', $myId)
            ->whereNull('DibacaPada')
            ->update(['DibacaPada' => now('Asia/Makassar')]);

        $chatUrl = $this->sebagaiSeller()
            ? route('seller.chat.show', $c->PercakapanID)
            : route('pelanggan.chat.show', $c->PercakapanID);

        Notifikasi::where('UserID', $myId)
            ->where('Tipe', 'chat_baru')
            ->where('Url', $chatUrl)
            ->whereNull('DibacaPada')
            ->update(['DibacaPada' => now('Asia/Makassar')]);
    }

    /** Pembeli menekan "Tanya Penjual" di halaman produk atau pesanan. */
    public function mulai(Request $request, $produkId)
    {
        $produk = Produk::where('Status', '!=', 'Disembunyikan')->findOrFail($produkId);
        $uid = (int) Auth::guard('web')->id();
        $sellerId = $produk->SellerID ? (int) $produk->SellerID : (int) \App\Models\User::where('is_seller', 1)->value('UserID');

        if (!$sellerId || $sellerId === $uid) {
            return back()->with('error', 'Anda tidak dapat mengirim pesan ke toko Anda sendiri.');
        }

        $c = Percakapan::firstOrCreate([
            'PembeliID' => $uid,
            'SellerID' => $sellerId,
        ]);

        $params = [
            'id' => $c->PercakapanID,
            'produk' => $produk->ProdukID,
        ];

        // Tentukan URL halaman sebelumnya (ref) agar tombol kembali bisa mengembalikan user ke tempat semula
        $ref = $request->input('ref') ?: url()->previous();
        if ($ref && !\Illuminate\Support\Str::contains($ref, '/chat/mulai')) {
            $params['ref'] = $ref;
        } else {
            $params['ref'] = route('pelanggan.produk.detail', $produk->ProdukID);
        }

        return redirect()->route('pelanggan.chat.show', $params);
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

        $pesan = Pesan::with([
                'produk' => function ($q) {
                    $q->select('ProdukID', 'NamaProduk', 'Harga', 'Gambar', 'Kondisi', 'Status', 'SellerID');
                },
                'tawaran.produk',
            ])
            ->where('PercakapanID', $c->PercakapanID)
            ->orderByDesc('PesanID')
            ->limit(200)
            ->get()
            ->reverse()
            ->values();

        $lawan = $t['sebagaiSeller'] ? $c->pembeli : $c->seller;

        // Produk yang sedang ditanyakan (dari parameter query atau dari riwayat pesan di percakapan ini)
        $isProdukBaru = request()->filled('produk');
        $produkRef = null;
        if ($isProdukBaru) {
            $produkRef = Produk::where('Status', '!=', 'Disembunyikan')
                ->find((int) request('produk'));
        }

        // Jika tidak ada di parameter URL, cari dari pesan terakhir dalam percakapan ini yang memiliki produk
        if (!$produkRef) {
            $lastProductMsg = $pesan->whereNotNull('ProdukID')->last();
            if ($lastProductMsg && $lastProductMsg->produk) {
                $produkRef = $lastProductMsg->produk;
            }
        }

        // Helper untuk memeriksa apakah URL aman/internal (mendukung localhost dan 127.0.0.1)
        $isInternalUrl = function ($urlStr) {
            if (empty($urlStr)) return false;
            if (\Illuminate\Support\Str::startsWith($urlStr, '/') && !\Illuminate\Support\Str::startsWith($urlStr, '//')) {
                return true;
            }
            $targetHost = parse_url($urlStr, PHP_URL_HOST);
            if (empty($targetHost)) return true;

            $currentHost = request()->getHost();
            $appHost = parse_url(config('app.url'), PHP_URL_HOST);
            $localHosts = ['localhost', '127.0.0.1'];

            if ($targetHost === $currentHost || $targetHost === $appHost) {
                return true;
            }
            if (in_array($targetHost, $localHosts, true) && (in_array($currentHost, $localHosts, true) || in_array($appHost, $localHosts, true))) {
                return true;
            }
            return false;
        };

        // Tentukan URL kembali (backUrl) secara cerdas:
        // 1. Parameter query ?ref= (jika valid URL internal dan bukan chat saat ini / mulai / logout)
        // 2. HTTP Referer (jika valid dan bukan chat / loop)
        // 3. Jika ada produk yang sedang dibahas, default kembali ke halaman detail produk
        // 4. Fallback akhir ke daftar pesan
        $backUrl = null;
        $backLabel = 'Kembali ke Halaman Sebelumnya';

        $refParam = request('ref');
        if ($refParam && $isInternalUrl($refParam)) {
            $currentPath = '/' . ltrim(request()->path(), '/');
            $refPath = parse_url($refParam, PHP_URL_PATH) ?? '';
            if (!\Illuminate\Support\Str::contains($refParam, '/chat/mulai') && 
                !\Illuminate\Support\Str::contains($refParam, '/logout') && 
                $refPath !== $currentPath) {
                $backUrl = $refParam;
                $backLabel = 'Kembali ke Halaman Sebelumnya';
            }
        }

        if (!$backUrl) {
            $prev = url()->previous();
            if ($prev && $isInternalUrl($prev)) {
                $currentFull = url()->full();
                $prevPath = parse_url($prev, PHP_URL_PATH) ?? '';
                $currentPath = '/' . ltrim(request()->path(), '/');
                if ($prev !== $currentFull && 
                    !\Illuminate\Support\Str::contains($prev, '/chat/mulai') && 
                    !\Illuminate\Support\Str::contains($prev, '/logout') && 
                    $prevPath !== $currentPath) {
                    $backUrl = $prev;
                    $backLabel = 'Kembali ke Halaman Sebelumnya';
                }
            }
        }

        if (!$backUrl && $produkRef) {
            $backUrl = route('pelanggan.produk.detail', $produkRef->ProdukID);
            $backLabel = 'Kembali ke Produk: ' . $produkRef->NamaProduk;
        }

        if (!$backUrl) {
            $backUrl = route($t['rp'] . '.index');
            $backLabel = 'Kembali ke Daftar Pesan';
        }

        return view('chat.show', array_merge($t, compact('c', 'pesan', 'lawan', 'produkRef', 'isProdukBaru', 'backUrl', 'backLabel')));
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

        $pesan = Pesan::create([
            'PercakapanID' => $c->PercakapanID,
            'PengirimID' => (int) $this->saya()->UserID,
            'ProdukID' => $produkId,
            'Isi' => trim($data['isi']),
            'DibuatPada' => now('Asia/Makassar'),
        ]);

        $c->TerakhirPada = now('Asia/Makassar');
        $c->save();

        // Kirimkan Notifikasi ke lawan bicara (Buyer <-> Seller)
        $isSellerSending = $this->sebagaiSeller();
        $penerimaId = $isSellerSending ? (int) $c->PembeliID : (int) $c->SellerID;
        $pengirimNama = $this->saya()->Nama ?? 'Pengguna';
        $chatUrl = $isSellerSending 
            ? route('pelanggan.chat.show', $c->PercakapanID)
            : route('seller.chat.show', $c->PercakapanID);

        $judulNotif = $isSellerSending 
            ? 'Pesan Baru dari ' . $pengirimNama . ' (Toko)'
            : 'Pesan Baru dari ' . $pengirimNama;

        // Jika ada notifikasi chat belum dibaca dari percakapan ini, update saja teks & waktunya agar tidak spam
        $notifLama = Notifikasi::where('UserID', $penerimaId)
            ->where('Tipe', 'chat_baru')
            ->where('Url', $chatUrl)
            ->whereNull('DibacaPada')
            ->latest('NotifikasiID')
            ->first();

        if ($notifLama) {
            $notifLama->update([
                'Judul' => $judulNotif,
                'Pesan' => \Illuminate\Support\Str::limit($pesan->Isi, 90),
                'DibuatPada' => now('Asia/Makassar'),
            ]);
        } else {
            Notifikasi::kirim(
                $penerimaId,
                'chat_baru',
                $judulNotif,
                \Illuminate\Support\Str::limit($pesan->Isi, 90),
                $chatUrl,
                'bi-chat-dots-fill',
                '#00B8BE'
            );
        }

        if ($request->expectsJson()) {
            $pesan->load([
                'produk' => function ($q) {
                    $q->select('ProdukID', 'NamaProduk', 'Harga', 'Gambar', 'Kondisi', 'Status', 'SellerID');
                },
                'tawaran.produk',
            ]);

            $t = $this->tampilan();
            return response()->json([
                'ok' => true,
                'pesan_id' => $pesan->PesanID,
                'html' => view('chat._pesan', [
                    'pesan' => collect([$pesan]),
                    'uid' => $t['uid'],
                    'sebagaiSeller' => $t['sebagaiSeller'],
                    'rp' => $t['rp'],
                    'percakapanId' => $c->PercakapanID,
                ])->render(),
            ]);
        }

        return redirect()->route($this->tampilan()['rp'] . '.show', $c->PercakapanID);
    }

    /** Dipanggil JavaScript tiap beberapa detik: ambil pesan yang lebih baru dari ?setelah=ID */
    public function pesan(Request $request, $id)
    {
        $c = $this->milik($id);
        $t = $this->tampilan();
        $this->tandaiDibaca($c);

        // Tutup session agar tidak menahan lock request lain
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        $setelah = (int) $request->query('setelah', 0);

        $baru = Pesan::with([
                'produk' => function ($q) {
                    $q->select('ProdukID', 'NamaProduk', 'Harga', 'Gambar', 'Kondisi', 'Status', 'SellerID');
                },
                'tawaran.produk',
            ])
            ->where('PercakapanID', $c->PercakapanID)
            ->where('PesanID', '>', $setelah)
            ->orderBy('PesanID')
            ->get();

        return response()->json([
            'html' => view('chat._pesan', [
                'pesan' => $baru,
                'uid' => $t['uid'],
                'sebagaiSeller' => $t['sebagaiSeller'],
                'rp' => $t['rp'],
                'percakapanId' => $c->PercakapanID,
            ])->render(),
            'terakhir' => (int) ($baru->max('PesanID') ?? $setelah),
            'jumlah' => $baru->count(),
        ]);
    }

    /** Update / edit isi pesan (hanya oleh pengirim) */
    public function updatePesan(Request $request, $id, $pesanId)
    {
        $c = $this->milik($id);

        $request->validate([
            'isi' => 'required|string|max:1000',
        ], [
            'isi.required' => 'Pesan tidak boleh kosong.',
            'isi.max' => 'Pesan maksimal 1000 karakter.',
        ]);

        $pesan = Pesan::where('PercakapanID', $c->PercakapanID)
            ->where('PesanID', $pesanId)
            ->where('PengirimID', (int) $this->saya()->UserID)
            ->firstOrFail();

        $pesan->Isi = trim($request->input('isi'));
        $pesan->save();

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'pesan_id' => $pesan->PesanID,
                'isi' => $pesan->Isi,
            ]);
        }

        return back()->with('success', 'Pesan berhasil diperbarui.');
    }

    /** Hapus pesan (hanya oleh pengirim) */
    public function hapusPesan(Request $request, $id, $pesanId)
    {
        $c = $this->milik($id);

        $pesan = Pesan::where('PercakapanID', $c->PercakapanID)
            ->where('PesanID', $pesanId)
            ->where('PengirimID', (int) $this->saya()->UserID)
            ->firstOrFail();

        $pesan->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'pesan_id' => (int) $pesanId,
            ]);
        }

        return back()->with('success', 'Pesan berhasil dihapus.');
    }

    /** Pembeli mengajukan penawaran harga di room chat */
    public function tawar(Request $request, $id)
    {
        $c = $this->milik($id);

        if ($this->sebagaiSeller()) {
            return response()->json(['ok' => false, 'pesan' => 'Hanya pembeli yang dapat mengajukan penawaran harga.'], 403);
        }

        $data = $request->validate([
            'produk_id' => 'required|integer',
            'harga_tawaran' => 'required|integer|min:1000',
            'catatan' => 'nullable|string|max:255',
        ], [
            'harga_tawaran.required' => 'Nominal tawaran harga wajib diisi.',
            'harga_tawaran.min' => 'Nominal tawaran minimal Rp 1.000.',
        ]);

        $produk = Produk::where('SellerID', $c->SellerID)
            ->where('Status', 'Tersedia')
            ->findOrFail((int) $data['produk_id']);

        $hargaTawaran = (int) $data['harga_tawaran'];
        if ($hargaTawaran >= (int) $produk->Harga) {
            return response()->json([
                'ok' => false,
                'pesan' => 'Harga tawaran harus lebih rendah dari harga awal (Rp ' . number_format($produk->Harga, 0, ',', '.') . ').',
            ], 422);
        }

        // Batalkan tawaran pending sebelumnya untuk produk ini di percakapan yang sama
        TawaranHarga::where('PercakapanID', $c->PercakapanID)
            ->where('ProdukID', $produk->ProdukID)
            ->where('Status', 'Menunggu')
            ->update(['Status' => 'Dibatalkan']);

        $uid = (int) $this->saya()->UserID;
        $catatan = !empty($data['catatan']) ? trim($data['catatan']) : null;

        $tawaran = TawaranHarga::create([
            'PercakapanID' => $c->PercakapanID,
            'PembeliID' => $uid,
            'SellerID' => $c->SellerID,
            'ProdukID' => $produk->ProdukID,
            'HargaAsli' => (int) $produk->Harga,
            'HargaTawaran' => $hargaTawaran,
            'Status' => 'Menunggu',
            'Catatan' => $catatan,
            'DibuatPada' => now('Asia/Makassar'),
        ]);

        $isiPesan = 'Mengajukan penawaran harga sebesar Rp ' . number_format($hargaTawaran, 0, ',', '.') . ' untuk ' . $produk->NamaProduk . '.';
        if ($catatan) {
            $isiPesan .= "\nCatatan: \"" . $catatan . '"';
        }

        $pesan = Pesan::create([
            'PercakapanID' => $c->PercakapanID,
            'PengirimID' => $uid,
            'ProdukID' => $produk->ProdukID,
            'TawaranID' => $tawaran->TawaranID,
            'TipePesan' => 'tawaran',
            'Isi' => $isiPesan,
            'DibuatPada' => now('Asia/Makassar'),
        ]);

        $tawaran->update(['PesanID' => $pesan->PesanID]);
        $c->update(['TerakhirPada' => now('Asia/Makassar')]);

        // Notifikasi ke Seller
        Notifikasi::kirim(
            $c->SellerID,
            'tawaran_masuk',
            'Penawaran Harga Baru!',
            $this->saya()->Nama . ' menawar "' . $produk->NamaProduk . '" seharga Rp ' . number_format($hargaTawaran, 0, ',', '.') . '.',
            route('seller.chat.show', $c->PercakapanID),
            'bi-tag-fill',
            '#00B8BE'
        );

        if ($request->expectsJson()) {
            $pesan->load([
                'produk' => function ($q) {
                    $q->select('ProdukID', 'NamaProduk', 'Harga', 'Gambar', 'Kondisi', 'Status', 'SellerID');
                },
                'tawaran.produk',
            ]);

            $t = $this->tampilan();
            return response()->json([
                'ok' => true,
                'pesan_id' => $pesan->PesanID,
                'html' => view('chat._pesan', [
                    'pesan' => collect([$pesan]),
                    'uid' => $t['uid'],
                    'sebagaiSeller' => $t['sebagaiSeller'],
                    'rp' => $t['rp'],
                    'percakapanId' => $c->PercakapanID,
                ])->render(),
            ]);
        }

        return redirect()->route($this->tampilan()['rp'] . '.show', $c->PercakapanID)
            ->with('success', 'Penawaran harga berhasil diajukan ke penjual.');
    }

    /** Penjual merespon tawaran harga (terima / tolak) */
    public function responTawaran(Request $request, $id, $tawaranId)
    {
        $c = $this->milik($id);

        if (!$this->sebagaiSeller()) {
            return response()->json(['ok' => false, 'pesan' => 'Hanya penjual yang dapat merespons tawaran harga.'], 403);
        }

        $data = $request->validate([
            'aksi' => 'required|in:terima,tolak',
        ]);

        $tawaran = TawaranHarga::with('produk')
            ->where('TawaranID', (int) $tawaranId)
            ->where('PercakapanID', $c->PercakapanID)
            ->where('SellerID', (int) $this->saya()->UserID)
            ->firstOrFail();

        if ($tawaran->Status !== 'Menunggu') {
            return response()->json(['ok' => false, 'pesan' => 'Tawaran ini telah direspon sebelumnya.'], 422);
        }

        $uid = (int) $this->saya()->UserID;
        $aksi = $data['aksi'];

        if ($aksi === 'terima') {
            $tawaran->update([
                'Status' => 'Diterima',
                'DiresponPada' => now('Asia/Makassar'),
            ]);

            $isiPesan = '🎉 Selamat! Penjual MENERIMA tawaran harga Rp ' . number_format($tawaran->HargaTawaran, 0, ',', '.') . ' untuk barang "' . ($tawaran->produk->NamaProduk ?? 'Produk') . '". Pembeli dapat langsung melakukan checkout sekarang.';
        } else {
            $tawaran->update([
                'Status' => 'Ditolak',
                'DiresponPada' => now('Asia/Makassar'),
            ]);

            $isiPesan = 'Mohon maaf, penjual belum dapat MENERIMA tawaran harga Rp ' . number_format($tawaran->HargaTawaran, 0, ',', '.') . '. Silakan ajukan tawaran lain yang sesuai.';
        }

        // Kirimkan notifikasi balasan resmi dari seller di balon chat
        $pesanBaru = Pesan::create([
            'PercakapanID' => $c->PercakapanID,
            'PengirimID' => $uid,
            'ProdukID' => $tawaran->ProdukID,
            'TawaranID' => $tawaran->TawaranID,
            'TipePesan' => 'teks',
            'Isi' => $isiPesan,
            'DibuatPada' => now('Asia/Makassar'),
        ]);

        $c->update(['TerakhirPada' => now('Asia/Makassar')]);

        // Notifikasi ke Pembeli
        if ($aksi === 'terima') {
            Notifikasi::kirim(
                $tawaran->PembeliID,
                'tawaran_diterima',
                'Tawaran Diterima Seller! 🎉',
                'Penjual menerima tawaran harga Anda Rp ' . number_format($tawaran->HargaTawaran, 0, ',', '.') . ' untuk "' . ($tawaran->produk->NamaProduk ?? 'Produk') . '". Yuk checkout sekarang!',
                route('pelanggan.checkout.show', [$tawaran->ProdukID, 'tawaran_id' => $tawaran->TawaranID]),
                'bi-bag-check-fill',
                '#10b981'
            );
        } else {
            Notifikasi::kirim(
                $tawaran->PembeliID,
                'tawaran_ditolak',
                'Tawaran Belum Disetujui',
                'Penjual belum dapat menerima tawaran harga Anda untuk "' . ($tawaran->produk->NamaProduk ?? 'Produk') . '". Anda dapat mengajukan tawaran baru.',
                route('pelanggan.chat.show', $c->PercakapanID),
                'bi-x-circle-fill',
                '#ef4444'
            );
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'status' => $tawaran->Status,
                'pesan' => 'Tawaran berhasil di' . ($aksi === 'terima' ? 'terima' : 'tolak') . '.',
            ]);
        }

        return redirect()->route($this->tampilan()['rp'] . '.show', $c->PercakapanID)
            ->with('success', 'Tawaran berhasil di' . ($aksi === 'terima' ? 'terima' : 'tolak') . '.');
    }

    /** Pembeli membatalkan tawarannya */
    public function batalTawaran(Request $request, $id, $tawaranId)
    {
        $c = $this->milik($id);

        if ($this->sebagaiSeller()) {
            return response()->json(['ok' => false, 'pesan' => 'Hanya pembeli yang dapat membatalkan tawaran.'], 403);
        }

        $tawaran = TawaranHarga::where('TawaranID', (int) $tawaranId)
            ->where('PercakapanID', $c->PercakapanID)
            ->where('PembeliID', (int) $this->saya()->UserID)
            ->firstOrFail();

        if ($tawaran->Status === 'Menunggu') {
            $tawaran->update([
                'Status' => 'Dibatalkan',
                'DiresponPada' => now('Asia/Makassar'),
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'pesan' => 'Tawaran berhasil dibatalkan.']);
        }

        return redirect()->route($this->tampilan()['rp'] . '.show', $c->PercakapanID)
            ->with('info', 'Tawaran harga dibatalkan.');
    }

    /** Jumlah pesan belum dibaca & notifikasi penanda real-time */
    public function unreadCount()
    {
        $u = $this->saya();
        if (!$u) {
            return response()->json(['unread' => 0]);
        }

        $isSeller = $this->sebagaiSeller();
        $unreadChat = $u->pesanBelumDibaca($isSeller);

        $res = [
            'unread' => $unreadChat,
            'is_seller' => $isSeller,
        ];

        if ($isSeller) {
            $res['pesanan_masuk'] = $u->pesananMasukPerluTindakanCount();
            $res['komisi_pending'] = $u->tagihanKomisiBelumDibayarCount();
        } else {
            $res['keranjang'] = $u->keranjangCount();
            $res['pesanan_aktif'] = $u->pesananAktifBuyerCount();
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        return response()->json($res);
    }
}
