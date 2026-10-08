@extends('layouts.main')

@section('content')
<div class="container py-5" style="margin-top: 80px;">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h1 class="h3 mb-3">Konfirmasi kata sandi</h1>
                    <p class="text-muted">Untuk melanjutkan, konfirmasikan kata sandi akun Anda.</p>
                    <form method="POST" action="{{ route('password.confirm') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="password" class="form-label">Kata sandi</label>
                            <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" required autofocus>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Konfirmasi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
