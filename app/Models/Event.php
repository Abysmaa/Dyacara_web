<?php

namespace App\Models;

use App\Enums\EventStatus;
use App\Support\EventDescriptionSanitizer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Event extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'description',
        'image',
        'event_date',
        'location',
        'status',
        'features',
    ];

    protected $casts = [
        'event_date' => 'date',
        'features'   => 'array',
        'status'     => EventStatus::class,
    ];

    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Event $event): void {
            $event->description = app(EventDescriptionSanitizer::class)->sanitize($event->description);
        });

        static::creating(function (Event $event): void {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->title);
            }
        });

        static::updating(function (Event $event): void {
            if ($event->isDirty('title') && ! $event->isDirty('slug')) {
                $event->slug = Str::slug($event->title);
            }
        });
    }

    public function getSanitizedDescriptionAttribute(): string
    {
        return app(EventDescriptionSanitizer::class)->sanitize($this->description);
    }
}
