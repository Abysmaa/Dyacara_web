<?php

namespace App\Filament\Resources;

use App\Enums\PaymentStatus;
use App\Filament\Resources\PaymentResource\Pages;
use App\Mail\PaymentStatusNotification;
use App\Models\Payment;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Pembayaran';
    protected static ?string $pluralModelLabel = 'Pembayaran';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon'),
                Tables\Columns\TextColumn::make('service')
                    ->label('Layanan'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah (DP 10%)')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode Pembayaran'),
                Tables\Columns\TextColumn::make('proof_image')
                    ->label('Bukti Pembayaran')
                    ->formatStateUsing(fn (): string => 'Lihat bukti pembayaran')
                    ->url(fn (Payment $record): string => route('admin.payments.proof', $record))
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->colors([
                        'warning' => PaymentStatus::Pending->value,
                        'success' => PaymentStatus::Verified->value,
                        'danger'  => PaymentStatus::Rejected->value,
                    ])
                    ->formatStateUsing(fn (string $state): string => PaymentStatus::from($state)->label()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->options([
                        'bank_transfer' => 'Transfer Bank',
                        'ewallet'       => 'E-Wallet',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(PaymentStatus::options()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('verify')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Payment $record): bool => $record->status === PaymentStatus::Pending)
                    ->action(function (Payment $record): void {
                        self::updatePaymentStatus($record, PaymentStatus::Verified);
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Payment $record): bool => $record->status === PaymentStatus::Pending)
                    ->action(function (Payment $record): void {
                        self::updatePaymentStatus($record, PaymentStatus::Rejected);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function updatePaymentStatus(Payment $payment, PaymentStatus $status): void
    {
        $payment->update(['status' => $status]);

        try {
            Mail::to($payment->email)->send(new PaymentStatusNotification($payment));
        } catch (TransportExceptionInterface $exception) {
            Log::error('Payment status notification email failed.', [
                'payment_id' => $payment->id,
                'status'     => $status->value,
                'exception'  => $exception,
            ]);

            Notification::make()
                ->title('Status diperbarui, tetapi email gagal dikirim')
                ->warning()
                ->send();

            return;
        }

        Notification::make()
            ->title($status === PaymentStatus::Verified ? 'Pembayaran berhasil diverifikasi' : 'Pembayaran ditolak')
            ->color($status === PaymentStatus::Verified ? 'success' : 'danger')
            ->send();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->label('Telepon')
                    ->required(),
                Forms\Components\TextInput::make('service')
                    ->label('Layanan')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->options([
                        'bank_transfer' => 'Transfer Bank',
                        'ewallet' => 'E-Wallet'
                    ])
                    ->required(),
                Forms\Components\FileUpload::make('proof_image')
                    ->label('Bukti Pembayaran')
                    ->image()
                    ->disk(config('filesystems.payment_proofs'))
                    ->directory('payment_proofs')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu Verifikasi',
                        'verified' => 'Terverifikasi',
                        'rejected' => 'Ditolak'
                    ])
                    ->required(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'view' => Pages\ViewPayment::route('/{record}'),
        ];
    }
}