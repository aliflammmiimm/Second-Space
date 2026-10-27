@extends('layouts.seller')
@section('title', 'Edit Produk - Seller')

@section('seller_content')
<div>
    <h4 class="fw-bold mb-3"><i class="bi bi-pencil-square text-danger me-2"></i> Edit Produk</h4>
    <form action="{{ route('seller.produk.update', $produk->ProdukID) }}" method="POST" enctype="multipart/form-data" class="card custom-card border-0 p-4">
        @csrf @method('PUT')
        @include('seller.produk._form')
        <div class="d-flex gap-2">
            <button class="btn btn-danger rounded-pill px-4 fw-semibold">Simpan Perubahan</button>
            <a href="{{ route('seller.produk.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Batal</a>
        </div>
    </form>
</div>
@endsection