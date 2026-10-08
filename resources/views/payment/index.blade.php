@extends('layouts.main')

@section('content')
<section class="py-5" style="margin-top: 80px;">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h1>Pembayaran Saya</h1>
                <p class="text-muted mb-0">Lihat status dan bukti konfirmasi pembayaran Anda.</p>
            </div>
            <a href="{{ route('services.index') }}" class="btn btn-primary">Lihat Layanan</a>
        </div>

        @forelse ($payments as $payment)
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="row align-items-center g-3">
                        <div class="col-md-4">
                            <h2 class="h5 mb-1">{{ $payment->service }}</h2>
                            <small class="text-muted">Dikirim {{ $payment->created_at->format('d M Y H:i') }}</small>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted">Uang muka (10%)</span>
                            <div class="fw-semibold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-md-2">
                            <span class="badge {{ match ($payment->status) {
                                'verified' => 'bg-success',
                                'rejected' => 'bg-danger',
                                default => 'bg-warning text-dark',
                            } }}">
                                {{ match ($payment->status) {
                                    'verified' => 'Terverifikasi',
                                    'rejected' => 'Ditolak',
                                    default => 'Menunggu Verifikasi',
                                } }}
                            </span>
                        </div>
                        <div class="col-md-3 text-md-end">
                            <a href="{{ route('payment.success', $payment) }}" class="btn btn-outline-primary">Lihat Rincian</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info">
                Anda belum memiliki pembayaran.
                <a href="{{ route('services.index') }}" class="alert-link">Jelajahi layanan kami</a>.
            </div>
        @endforelse

        <div class="d-flex justify-content-center mt-4">
            {{ $payments->links() }}
        </div>
    </div>
</section>
@endsection
