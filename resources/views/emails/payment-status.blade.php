@component('mail::message')
# Pembaruan Status Pembayaran

Halo {{ $payment->name }},

Status pembayaran Anda untuk layanan **{{ $payment->service }}** telah diperbarui menjadi:

**{{ $payment->status === 'verified' ? 'Terverifikasi' : 'Ditolak' }}**

Uang muka: Rp {{ number_format($payment->amount, 0, ',', '.') }}

@if ($payment->status === 'verified')
Pembayaran Anda telah diverifikasi. Tim kami akan menghubungi Anda untuk langkah selanjutnya.
@else
Silakan hubungi tim kami jika Anda memerlukan informasi lebih lanjut mengenai pembayaran ini.
@endif

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
