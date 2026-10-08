<?php

use App\Models\Booking;
use App\Models\Event;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use App\Filament\Resources\BookingResource\Pages\ListBookings;
use App\Filament\Resources\PaymentResource\Pages\ListPayments;
use App\Mail\BookingStatusNotification;
use App\Mail\PaymentNotification;
use App\Mail\PaymentStatusNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

test('the framework health endpoint is available', function () {
    $this->get('/up')->assertOk();
});

test('event search applies every supplied filter without losing the category condition', function () {
    Event::create([
        'title' => 'Art Engagement',
        'category' => 'engagement',
        'description' => 'A celebration in Bogor',
        'event_date' => '2027-01-05',
        'location' => 'Bogor',
        'status' => 'upcoming',
    ]);

    Event::create([
        'title' => 'Art Gathering',
        'category' => 'gathering',
        'description' => 'A celebration in Bogor',
        'event_date' => '2027-01-05',
        'location' => 'Bogor',
        'status' => 'upcoming',
    ]);

    $response = $this->get('/events/search?category=engagement&location=Bogor&date=2027-01-05&search=Art');

    $response->assertOk()
        ->assertSee('Art Engagement')
        ->assertDontSee('Art Gathering');
});

test('event descriptions keep safe formatting and remove unsafe html when saved', function () {
    $event = Event::create([
        'title' => 'Sanitized Event',
        'category' => 'engagement',
        'description' => '<p>Safe <strong>formatting</strong></p><script>alert("xss")</script><a href="javascript:alert(1)">unsafe link</a>',
        'event_date' => today()->addDays(2),
        'location' => 'Bogor',
        'status' => 'upcoming',
    ]);

    expect($event->fresh()->description)
        ->toContain('<p>')
        ->toContain('<strong>formatting</strong>')
        ->not->toContain('<script')
        ->not->toContain('javascript:');

    $this->get(route('events.show', $event))
        ->assertOk()
        ->assertSee('<strong>formatting</strong>', false)
        ->assertDontSee('alert("xss")', false)
        ->assertDontSee('javascript:alert', false);
});

test('an authenticated user can request a booking for an event', function () {
    $user = User::factory()->create();
    $event = Event::create([
        'title' => 'Community Gathering',
        'category' => 'gathering',
        'description' => 'A community event',
        'event_date' => today()->addDays(10),
        'location' => 'Bogor',
        'status' => 'upcoming',
    ]);

    $response = $this->actingAs($user)->post(route('events.book', $event), [
        'phone' => '+6281234567890',
        'notes' => 'Please contact me in the afternoon.',
    ]);

    $response->assertRedirect(route('events.show', $event))
        ->assertSessionHas('success');

    $booking = Booking::firstOrFail();
    expect($booking->event_id)->toBe($event->id)
        ->and($booking->user_id)->toBe($user->id)
        ->and($booking->name)->toBe($user->name)
        ->and($booking->email)->toBe($user->email)
        ->and($booking->status)->toBe('pending');
});

test('users cannot submit a duplicate active booking for the same event', function () {
    $user = User::factory()->create();
    $event = Event::create([
        'title' => 'Single Booking Event',
        'category' => 'gathering',
        'description' => 'A future event',
        'event_date' => today()->addDays(10),
        'location' => 'Bogor',
        'status' => 'upcoming',
    ]);

    $this->actingAs($user)->post(route('events.book', $event), [
        'phone' => '+6281234567890',
    ])->assertRedirect();

    $this->post(route('events.book', $event), [
        'phone' => '+6281234567890',
    ])->assertSessionHasErrors('event');

    expect(Booking::where('event_id', $event->id)->where('user_id', $user->id)->count())->toBe(1);
});

test('users cannot book past or completed events', function () {
    $user = User::factory()->create();
    $pastEvent = Event::create([
        'title' => 'Past Event',
        'category' => 'gathering',
        'description' => 'An event in the past',
        'event_date' => today()->subDay(),
        'location' => 'Bogor',
        'status' => 'completed',
    ]);
    $completedFutureEvent = Event::create([
        'title' => 'Completed Event',
        'category' => 'engagement',
        'description' => 'An event marked completed',
        'event_date' => today()->addDays(10),
        'location' => 'Bogor',
        'status' => 'completed',
    ]);

    $this->actingAs($user)->post(route('events.book', $pastEvent), [
        'phone' => '+6281234567890',
    ])->assertSessionHasErrors('event');

    $this->post(route('events.book', $completedFutureEvent), [
        'phone' => '+6281234567890',
    ])->assertSessionHasErrors('event');

    expect(Booking::count())->toBe(0);
});

test('a user can submit a new request after their previous booking was rejected', function () {
    $user = User::factory()->create();
    $event = Event::create([
        'title' => 'Rebookable Event',
        'category' => 'engagement',
        'description' => 'A future event',
        'event_date' => today()->addDays(10),
        'location' => 'Bogor',
        'status' => 'upcoming',
    ]);
    $booking = Booking::create([
        'event_id' => $event->id,
        'user_id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'phone' => '+6281234567890',
        'date' => $event->event_date,
        'status' => 'rejected',
    ]);

    $this->actingAs($user)->post(route('events.book', $event), [
        'phone' => '+6281234567890',
        'notes' => 'Please reconsider.',
    ])->assertRedirect(route('events.show', $event));

    expect(Booking::where('event_id', $event->id)->where('user_id', $user->id)->count())->toBe(1)
        ->and($booking->fresh()->status)->toBe('pending')
        ->and($booking->fresh()->notes)->toBe('Please reconsider.');
});

test('users only see their own bookings in booking history', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $event = Event::create([
        'title' => 'My Private Booking',
        'category' => 'engagement',
        'description' => 'Private event',
        'event_date' => '2027-02-15',
        'location' => 'Bogor',
        'status' => 'upcoming',
    ]);

    Booking::create([
        'event_id' => $event->id,
        'user_id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'phone' => '+6281234567890',
        'date' => '2027-02-15',
        'status' => 'pending',
    ]);

    Booking::create([
        'event_id' => Event::create([
            'title' => 'Other Booking Secret',
            'category' => 'engagement',
            'description' => 'Another private event',
            'event_date' => '2027-02-16',
            'location' => 'Bogor',
            'status' => 'upcoming',
        ])->id,
        'user_id' => $otherUser->id,
        'name' => $otherUser->name,
        'email' => $otherUser->email,
        'phone' => '+6281234567890',
        'date' => '2027-02-15',
        'status' => 'pending',
    ]);

    $this->actingAs($user)
        ->get(route('bookings.index'))
        ->assertOk()
        ->assertSee('My Private Booking')
        ->assertDontSee('Other Booking Secret');
});

test('users can view only their own payment history', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $attributes = [
        'name' => $user->name,
        'phone' => '+6281234567890',
        'service' => 'Wedding Planning',
        'amount' => 10000,
        'payment_method' => 'bank_transfer',
        'proof_image' => 'payment_proofs/proof.jpg',
    ];

    Payment::create($attributes + [
        'user_id' => $user->id,
        'email' => $user->email,
    ]);

    Payment::create($attributes + [
        'name' => 'Other Payment Secret',
        'user_id' => $otherUser->id,
        'email' => $otherUser->email,
    ]);

    $this->actingAs($user)
        ->get(route('payments'))
        ->assertOk()
        ->assertSee('Wedding Planning')
        ->assertDontSee('Other Payment Secret');
});

test('admins can open the booking management page', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->get('/admin/bookings')
        ->assertOk()
        ->assertSee('Pemesanan Event');
});

test('non-admin users cannot access the booking management page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/admin/bookings')
        ->assertForbidden();
});

test('admins can confirm a pending booking', function () {
    Mail::fake();

    $customer = User::factory()->create();
    $admin = User::factory()->create(['is_admin' => true]);
    $event = Event::create([
        'title' => 'Confirmed Event',
        'category' => 'engagement',
        'description' => 'An event awaiting confirmation',
        'event_date' => '2027-03-20',
        'location' => 'Bogor',
        'status' => 'upcoming',
    ]);
    $booking = Booking::create([
        'event_id' => $event->id,
        'user_id' => $customer->id,
        'name' => $customer->name,
        'email' => $customer->email,
        'phone' => '+6281234567890',
        'date' => '2027-03-20',
        'status' => 'pending',
    ]);

    \Livewire\Livewire::actingAs($admin)
        ->test(ListBookings::class)
        ->callTableAction('confirm', $booking);

    expect($booking->fresh()->status)->toBe('confirmed');
    Mail::assertSent(BookingStatusNotification::class, fn (BookingStatusNotification $mail): bool =>
        $mail->booking->is($booking->fresh())
    );

    $this->actingAs($customer)
        ->get(route('bookings.index'))
        ->assertOk()
        ->assertSee('Dikonfirmasi');
});

test('admins notify the customer when rejecting a pending booking', function () {
    Mail::fake();

    $admin = User::factory()->create(['is_admin' => true]);
    $event = Event::create([
        'title' => 'Rejected Event',
        'category' => 'gathering',
        'description' => 'An event awaiting a decision',
        'event_date' => '2027-03-21',
        'location' => 'Bogor',
        'status' => 'upcoming',
    ]);
    $booking = Booking::create([
        'event_id' => $event->id,
        'name' => 'Client Name',
        'email' => 'client@example.com',
        'phone' => '+6281234567890',
        'date' => '2027-03-21',
        'status' => 'pending',
    ]);

    \Livewire\Livewire::actingAs($admin)
        ->test(ListBookings::class)
        ->callTableAction('reject', $booking);

    expect($booking->fresh()->status)->toBe('rejected');
    Mail::assertSent(BookingStatusNotification::class, fn (BookingStatusNotification $mail): bool =>
        $mail->booking->is($booking->fresh())
    );
});

test('payment deposit is calculated from the active service rather than submitted form data', function () {
    Storage::fake('local');
    Storage::fake('public');
    Storage::fake('s3');
    config(['filesystems.payment_proofs' => 's3']);
    Mail::fake();

    $user = User::factory()->create();
    $service = Service::create([
        'name' => 'Wedding Planning',
        'description' => 'Complete wedding planning service',
        'price' => 100000,
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->post(route('payment.confirm'), [
        'name' => $user->name,
        'email' => $user->email,
        'phone' => '+6281234567890',
        'payment_method' => 'bank_transfer',
        'service' => $service->slug,
        'amount' => 1,
        'proof' => UploadedFile::fake()->image('proof.jpg'),
    ]);

    $payment = Payment::firstOrFail();
    expect((float) $payment->amount)->toBe(10000.0)
        ->and($payment->service)->toBe($service->name)
        ->and($payment->user_id)->toBe($user->id);
    Storage::disk('s3')->assertExists($payment->proof_image);
    Storage::disk('local')->assertMissing($payment->proof_image);
    Storage::disk('public')->assertMissing($payment->proof_image);

    $response->assertRedirect(route('payment.success', $payment->id));
    Mail::assertSent(PaymentNotification::class, fn (PaymentNotification $mail): bool =>
        str_contains($mail->render(), '10.000')
    );
});

test('only admins can view payment proof files', function () {
    Storage::fake('local');
    Storage::fake('public');

    $proofPath = UploadedFile::fake()->image('proof.jpg')->store('payment_proofs', 'local');
    $customer = User::factory()->create();
    $admin = User::factory()->create(['is_admin' => true]);
    $payment = Payment::create([
        'user_id' => $customer->id,
        'name' => $customer->name,
        'email' => $customer->email,
        'phone' => '+6281234567890',
        'service' => 'Wedding Planning',
        'amount' => 10000,
        'payment_method' => 'bank_transfer',
        'proof_image' => $proofPath,
    ]);

    $proofResponse = $this->actingAs($admin)
        ->get(route('admin.payments.proof', $payment));

    $proofResponse->assertOk();
    expect($proofResponse->headers->get('Cache-Control'))->toContain('no-store');

    $this->actingAs(User::factory()->create())
        ->get(route('admin.payments.proof', $payment))
        ->assertForbidden();
});

test('admins notify the customer when verifying a payment', function () {
    Mail::fake();

    $customer = User::factory()->create();
    $admin = User::factory()->create(['is_admin' => true]);
    $payment = Payment::create([
        'user_id' => $customer->id,
        'name' => $customer->name,
        'email' => $customer->email,
        'phone' => '+6281234567890',
        'service' => 'Wedding Planning',
        'amount' => 10000,
        'payment_method' => 'bank_transfer',
        'proof_image' => 'payment_proofs/proof.jpg',
        'status' => 'pending',
    ]);

    \Livewire\Livewire::actingAs($admin)
        ->test(ListPayments::class)
        ->callTableAction('verify', $payment);

    expect($payment->fresh()->status)->toBe('verified');
    Mail::assertSent(PaymentStatusNotification::class, fn (PaymentStatusNotification $mail): bool =>
        $mail->payment->is($payment->fresh())
    );

    $this->actingAs($customer)
        ->get(route('payments'))
        ->assertOk()
        ->assertSee('Terverifikasi');
});

test('admins notify the customer when rejecting a payment', function () {
    Mail::fake();

    $admin = User::factory()->create(['is_admin' => true]);
    $payment = Payment::create([
        'name' => 'Client Name',
        'email' => 'client@example.com',
        'phone' => '+6281234567890',
        'service' => 'Wedding Planning',
        'amount' => 10000,
        'payment_method' => 'bank_transfer',
        'proof_image' => 'payment_proofs/proof.jpg',
        'status' => 'pending',
    ]);

    \Livewire\Livewire::actingAs($admin)
        ->test(ListPayments::class)
        ->callTableAction('reject', $payment);

    expect($payment->fresh()->status)->toBe('rejected');
    Mail::assertSent(PaymentStatusNotification::class, fn (PaymentStatusNotification $mail): bool =>
        $mail->payment->is($payment->fresh())
    );
});

test('a user cannot view another users payment receipt', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $payment = Payment::create([
        'user_id' => $owner->id,
        'name' => $owner->name,
        'email' => $owner->email,
        'phone' => '+6281234567890',
        'service' => 'Wedding Planning',
        'amount' => 10000,
        'payment_method' => 'bank_transfer',
        'proof_image' => 'payment_proofs/proof.jpg',
    ]);

    $this->actingAs($otherUser)
        ->get(route('payment.success', $payment->id))
        ->assertNotFound();
});
