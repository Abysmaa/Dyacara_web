@extends('layouts.main')

@section('content')
    <div class="container py-5" style="margin-top: 80px;">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">
                        @if (session('warning'))
                            <div class="alert alert-warning">{{ session('warning') }}</div>
                        @endif
                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <h3 class="text-dark mb-1">INVOICE</h3>
                                <small class="text-muted">Status:
                                    <span class="badge {{ match ($payment->status) {
                                        'verified' => 'bg-success',
                                        'rejected' => 'bg-danger',
                                        default => 'bg-warning',
                                    } }}">
                                        {{ match ($payment->status) {
                                            'verified' => 'Terverifikasi',
                                            'rejected' => 'Ditolak',
                                            default => 'Menunggu Verifikasi',
                                        } }}
                                    </span>
                                </small>
                            </div>
                            <div class="col-sm-6 text-sm-end">
                                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 50px;">
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <h6 class="mb-3">Detail Pembayaran:</h6>
                                <div><strong>{{ $payment->name }}</strong></div>
                                <div>{{ $payment->email }}</div>
                                <div>{{ $payment->phone }}</div>
                            </div>
                            <div class="col-sm-6 text-sm-end">
                                <h6 class="mb-3">Tanggal:</h6>
                                <div>{{ $payment->created_at->format('d M Y') }}</div>
                                <div class="mt-2">
                                    <small>Metode: {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive-sm mb-4">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Deskripsi</th>
                                        <th class="text-end">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Uang muka 10% - {{ $payment->service }}</td>
                                        <td class="text-end">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total uang muka</th>
                                        <th class="text-end">Rp {{ number_format($payment->amount, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-sm-8">
                                <p class="text-muted mb-0">
                                    <strong>Catatan:</strong><br>
                                    Tim kami akan memverifikasi pembayaran Anda dan menghubungi Anda segera.
                                    Simpan bukti pembayaran ini untuk referensi di masa mendatang.
                                </p>
                            </div>
                            <div class="col-sm-4 text-sm-end">
                                <a href="{{ route('home') }}" class="btn btn-primary w-100">Kembali ke Beranda</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection