<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Service;
use App\Mail\PaymentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = $request->user()
            ->payments()
            ->latest()
            ->paginate(10);

        return view('payment.index', compact('payments'));
    }

    public function show(string $service)
    {
        $serviceRecord = Service::query()
            ->where('slug', $service)
            ->where('is_active', true)
            ->where('price', '>', 0)
            ->firstOrFail();

        $service      = $serviceRecord->name;
        $serviceSlug  = $serviceRecord->slug;
        $amount       = $serviceRecord->price;
        $depositAmount = round((float) $amount * 0.1, 2);

        return view('payment.show', compact('service', 'serviceSlug', 'amount', 'depositAmount'));
    }

    public function confirm(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'phone'          => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'payment_method' => 'required|in:bank_transfer,ewallet',
            'proof'          => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'service'        => [
                'required',
                'string',
                Rule::exists('services', 'slug')->where('is_active', true),
            ],
        ]);

        $service = Service::query()
            ->where('slug', $validated['service'])
            ->where('is_active', true)
            ->where('price', '>', 0)
            ->firstOrFail();

        $proofDisk = config('filesystems.payment_proofs');
        $proofPath = $request->file('proof')->store('payment_proofs', $proofDisk);

        $payment = Payment::create([
            'user_id'        => $request->user()->id,
            'service_id'     => $service->id,
            'name'           => $validated['name'],
            'email'          => $validated['email'],
            'phone'          => $validated['phone'],
            'service'        => $service->name,
            'amount'         => round((float) $service->price * 0.1, 2),
            'payment_method' => $validated['payment_method'],
            'proof_image'    => $proofPath,
            'status'         => PaymentStatus::Pending,
        ]);

        try {
            Mail::to($payment->email)->send(new PaymentNotification($payment));
        } catch (TransportExceptionInterface $e) {
            Log::error('Payment notification email failed.', [
                'payment_id' => $payment->id,
                'exception'  => $e,
            ]);

            return redirect()->route('payment.success', $payment->id)
                ->with('warning', 'Pembayaran tercatat, tetapi email konfirmasi gagal dikirim. Tim kami tetap akan memprosesnya.');
        }

        return redirect()->route('payment.success', $payment->id)
            ->with('success', 'Pembayaran berhasil dikonfirmasi! Tim kami akan segera memproses pesanan Anda.');
    }

    public function success(Request $request, $id)
    {
        $payment = Payment::where('user_id', $request->user()->id)->findOrFail($id);
        return view('payment.success', compact('payment'));
    }

    public function proof(Payment $payment)
    {
        $privateDisk = Storage::disk(config('filesystems.payment_proofs'));

        if ($privateDisk->exists($payment->proof_image)) {
            return $privateDisk->response(
                $payment->proof_image,
                headers: ['Cache-Control' => 'private, no-store'],
            );
        }

        $legacyPublicDisk = Storage::disk('public');

        abort_unless($legacyPublicDisk->exists($payment->proof_image), 404);

        return $legacyPublicDisk->response(
            $payment->proof_image,
            headers: ['Cache-Control' => 'private, no-store'],
        );
    }
}
