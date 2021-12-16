<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Picture extends Model
{
    use HasFactory;
    use HasSlug;

    protected $fillable = [
        'title',
        'description',
        'url',
        'year',
        'featured',
    ];

    protected $appends = ['person_ids'];

    /**
     * Return temporary s3 url of path if full path doesn't exist.
     */
    public function getUrlAttribute($value): string
    {
        if (Str::startsWith($value, 'https://')) {
            return $value;
        }

        return app()->environment('testing')
            ? Storage::url($value)
            : Storage::temporaryUrl($value, now()->addMinutes(5));
    }

    /**
     * Always return title capitalized.
     */
    public function getTitleAttribute($value): string
    {
        return ucwords($value);
    }

    /**
     * Always set title lowercase.
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = strtolower($value);
    }

    public function getPersonIdsAttribute()
    {
        return $this->people->modelKeys();
    }

    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class)
            ->select(['people.id', 'slug', 'first_name', 'last_name']);
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
