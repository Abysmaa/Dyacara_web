@component('mail::message')
# Pembaruan Pemesanan Event

Halo {{ $booking->name }},

Status permintaan Anda untuk event **{{ $booking->event?->title ?? 'event yang Anda pilih' }}** telah diperbarui menjadi:

**{{ $booking->status === 'confirmed' ? 'Dikonfirmasi' : 'Ditolak' }}**

Tanggal event: {{ $booking->date->format('d M Y') }}

@if ($booking->status === 'confirmed')
Tim kami akan menghubungi Anda untuk langkah selanjutnya.
@else
Silakan hubungi tim kami jika Anda memerlukan informasi lebih lanjut.
@endif

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
