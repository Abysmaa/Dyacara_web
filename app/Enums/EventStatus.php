<?php

namespace App\Enums;

enum EventStatus: string
{
    case Upcoming  = 'upcoming';
    case Ongoing   = 'ongoing';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Upcoming  => 'Akan Datang',
            self::Ongoing   => 'Sedang Berlangsung',
            self::Completed => 'Selesai',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Upcoming  => 'info',
            self::Ongoing   => 'success',
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
