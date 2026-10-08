@component('mail::message')
# Notifikasi Pembayaran Baru

Pembayaran baru telah diterima dengan detail sebagai berikut:

**Nama:** {{ $payment->name }}  
**Email:** {{ $payment->email }}  
**Telepon:** {{ $payment->phone }}  
**Layanan:** {{ $payment->service }}  
**Uang muka (10%):** Rp {{ number_format($payment->amount, 0, ',', '.') }}<br>
**Metode:** {{ $payment->payment_method }}  

Terima kasih telah melakukan pembayaran. Tim kami akan segera memverifikasi pembayaran Anda.

Salam,<br>
{{ config('app.name') }}
@endcomponent