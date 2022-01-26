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
        'background_photo_url',
    ];

    /**
     * Return s3 url of path if full path doesn't exist.
     */
    public function getBackgroundPhotoUrlAttribute($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (Str::startsWith($value, 'https://')) {
            return $value;
        }

        return Storage::url($value);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
