@extends('layouts.main')

@section('content')
<div class="container py-5" style="margin-top: 80px;">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h1 class="h3 mb-3">Atur ulang kata sandi</h1>
                    <form method="POST" action="{{ route('password.store') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $email) }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Kata sandi baru</label>
                            <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi kata sandi</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Simpan Kata Sandi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
