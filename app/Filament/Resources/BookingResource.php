<?php

namespace App\Filament\Resources;

use App\Enums\BookingStatus;
use App\Filament\Resources\BookingResource\Pages;
use App\Mail\BookingStatusNotification;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Pemesanan Event';
    protected static ?string $pluralModelLabel = 'Pemesanan Event';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Placeholder::make('event')
                ->label('Event')
                ->content(fn (?Booking $record): string => $record?->event?->title ?? '-'),
            Forms\Components\Placeholder::make('name')
                ->label('Nama Pemesan'),
            Forms\Components\Placeholder::make('email')
                ->label('Email'),
            Forms\Components\Placeholder::make('phone')
                ->label('Telepon'),
            Forms\Components\Placeholder::make('date')
                ->label('Tanggal Event')
                ->content(fn (?Booking $record): string => $record?->date?->format('d M Y H:i') ?? '-'),
            Forms\Components\Placeholder::make('notes')
                ->label('Catatan')
                ->content(fn (?Booking $record): string => $record?->notes ?: '-'),
            Forms\Components\Placeholder::make('status')
                ->label('Status')
                ->content(fn (?Booking $record): string => match ($record?->status) {
                    'pending' => 'Menunggu Konfirmasi',
                    'confirmed' => 'Dikonfirmasi',
                    'rejected' => 'Ditolak',
                    'completed' => 'Selesai',
                    default => $record?->status ?? '-',
                }),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event.title')
                    ->label('Event')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Pemesan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon'),
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal Event')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'warning' => BookingStatus::Pending->value,
                        'success' => BookingStatus::Confirmed->value,
                        'danger'  => BookingStatus::Rejected->value,
                        'gray'    => BookingStatus::Completed->value,
                    ])
                    ->formatStateUsing(fn (string $state): string => BookingStatus::from($state)->label()),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Permintaan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(BookingStatus::options()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('confirm')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Booking $record): bool => $record->status === BookingStatus::Pending)
                    ->action(function (Booking $record): void {
                        self::updateBookingStatus($record, BookingStatus::Confirmed);
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Booking $record): bool => $record->status === BookingStatus::Pending)
                    ->action(function (Booking $record): void {
                        self::updateBookingStatus($record, BookingStatus::Rejected);
                    }),
            ]);
    }

    private static function updateBookingStatus(Booking $booking, BookingStatus $status): void
    {
        $booking->update(['status' => $status]);

        try {
            Mail::to($booking->email)->send(new BookingStatusNotification($booking));
        } catch (TransportExceptionInterface $exception) {
            Log::error('Booking status notification email failed.', [
                'booking_id' => $booking->id,
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
            ->title($status === BookingStatus::Confirmed ? 'Pemesanan berhasil dikonfirmasi' : 'Pemesanan ditolak')
            ->color($status === BookingStatus::Confirmed ? 'success' : 'danger')
            ->send();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'view' => Pages\ViewBooking::route('/{record}'),
        ];
    }
}
