@extends('layouts.main')

@section('content')
<div class="container py-5" style="margin-top: 80px;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Form Pembayaran {{ $service }}</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <h5>Detail Pembayaran:</h5>
                        <p>Layanan: {{ $service }}</p>
                        <p>Total Pembayaran: Rp {{ number_format($amount, 0, ',', '.') }}</p>
                        <p>DP (10%): Rp {{ number_format($depositAmount, 0, ',', '.') }}</p>
                        
                        <h5 class="mt-4">Informasi Rekening:</h5>
                        <p class="mb-2">Transfer Bank:</p>
                        <ul class="list-unstyled">
                            <li>Bank Mandiri: 1170010129963</li>
                            <li>Atas Nama: PT Dyacara Indonesia</li>
                        </ul>
                        <p class="mb-2">E-Wallet:</p>
                        <ul class="list-unstyled">
                            <li>DANA: 087881465628</li>
                            <li>OVO: 087881465628</li>
                            <li>GoPay: 087881465628</li>
                        </ul>
                    </div>

                    <form action="{{ route('payment.confirm') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="service" value="{{ $serviceSlug }}">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" class="form-control" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran</label>
                            <select class="form-select" name="payment_method" required>
                                <option value="">Pilih Metode Pembayaran</option>
                                <option value="bank_transfer">Transfer Bank</option>
                                <option value="ewallet">E-Wallet</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bukti Pembayaran</label>
                            <input type="file" class="form-control" name="proof" accept="image/*" required>
                            <small class="text-muted">Format: JPG, PNG, maksimal 2MB</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Konfirmasi Pembayaran</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection