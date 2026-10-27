@extends('layouts.seller')
@section('title', 'Tambah Produk - Seller')

@section('seller_content')
<div>
    <h4 class="fw-bold mb-3"><i class="bi bi-plus-circle text-danger me-2"></i> Tambah Produk</h4>
    <form action="{{ route('seller.produk.store') }}" method="POST" enctype="multipart/form-data" class="card custom-card border-0 p-4">
        @csrf
        @include('seller.produk._form')
        <div class="d-flex gap-2">
            <button class="btn btn-danger rounded-pill px-4 fw-semibold">Simpan</button>
            <a href="{{ route('seller.produk.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Batal</a>
        </div>
    </form>
</div>
@endsection