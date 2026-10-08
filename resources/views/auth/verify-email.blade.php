@extends('layouts.main')

@section('content')
<div class="container py-5" style="margin-top: 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h1 class="h3 mb-3">Verifikasi email</h1>
                    <p class="text-muted">Silakan verifikasi alamat email melalui tautan yang kami kirim. Jika belum menerima email, Anda dapat meminta tautan baru.</p>
                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success">Tautan verifikasi baru telah dikirim.</div>
                    @endif
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">Kirim ulang tautan verifikasi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
