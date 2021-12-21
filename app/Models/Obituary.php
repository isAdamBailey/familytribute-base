<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Obituary extends Model
{
    use HasFactory;

    protected $fillable = [
        'person_id',
        'birth_date',
        'death_date',
        'content',
        'main_photo_url',
        'background_photo_url',
    ];

    /**
     * Return temporary s3 url of path if full path doesn't exist.
     */
    public function getMainPhotoUrlAttribute($value): string
    {
        if (empty($value)) {
            return $this->defaultMainPhotoUrl();
        }

        if (Str::startsWith($value, 'https://')) {
            return $value;
        }

        return app()->environment('testing')
            ? Storage::url($value)
            : Storage::temporaryUrl($value, now()->addMinutes(5));
    }

    /**
     * Return temporary s3 url of path if full path doesn't exist.
     */
    public function getBackgroundPhotoUrlAttribute($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (Str::startsWith($value, 'https://')) {
            return $value;
        }

        return app()->environment('testing')
            ? Storage::url($value)
            : Storage::temporaryUrl($value, now()->addMinutes(5));
    }

    protected function defaultMainPhotoUrl(): string
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($this->person->full_name).'&color=374151&background=F3F4F6';
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
