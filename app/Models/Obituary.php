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
        'headstone_url',
    ];

    /**
     * Return temporary s3 url of path if full path doesn't exist.
     */
    public function getHeadstoneUrlAttribute($value): string
    {
        if (empty($value)) {
            return $this->defaultHeadstoneUrl();
        }

        if (Str::startsWith($value, 'https://')) {
            return $value;
        }

        return app()->environment('testing')
            ? Storage::url($value)
            : Storage::temporaryUrl($value, now()->addMinutes(5));
    }

    protected function defaultHeadstoneUrl(): string
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($this->person->full_name).'&color=374151&background=F3F4F6';
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
