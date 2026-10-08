<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Payment $payment)
    {
    }

    public function build(): self
    {
        return $this->markdown('emails.payment-status')
            ->subject('Pembaruan Status Pembayaran - DYACARA');
    }
}
