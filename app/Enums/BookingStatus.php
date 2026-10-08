<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Pending   = 'pending';
    case Confirmed = 'confirmed';
    case Rejected  = 'rejected';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Pending   => 'Menunggu Konfirmasi',
            self::Confirmed => 'Dikonfirmasi',
            self::Rejected  => 'Ditolak',
            self::Completed => 'Selesai',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending   => 'warning',
            self::Confirmed => 'success',
            self::Rejected  => 'danger',
            self::Completed => 'gray',
        };
    }

    public static function options(): array
    {
        return array_column(
            array_map(fn ($case) => ['value' => $case->value, 'label' => $case->label()], self::cases()),
            'label',
            'value'
        );
    }
}
