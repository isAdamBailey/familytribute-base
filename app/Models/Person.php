<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Person extends Model
{
    use HasFactory;
    use HasSlug;

    protected $fillable = [
        'first_name',
        'last_name',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['full_name'];

    /**
     * Get the obituary's full name.
     */
    public function getFullNameAttribute(): string
    {
        return ucwords("{$this->first_name} {$this->last_name}");
    }

    /**
     * Always set first_name lowercase.
     */
    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = strtolower($value);
    }

    /**
     * Always set last_name lowercase.
     */
    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = strtolower($value);
    }

    public function obituary(): HasOne
    {
        return $this->hasOne(Obituary::class)
            ->select(['id', 'person_id', 'birth_date', 'death_date', 'main_photo_url', 'content', 'background_photo_url']);
    }

    public function pictures(): BelongsToMany
    {
        return $this->belongsToMany(Picture::class)
            ->when(! auth()->user(),
                fn ($query) => $query->where('private', '!=', 1)
            )
            ->select(['slug', 'url', 'title', 'description', 'year'])
            ->orderBy('year');
    }

    public function stories(): BelongsToMany
    {
        return $this->belongsToMany(Story::class)
            ->when(! auth()->user(),
                fn ($query) => $query->where('private', '!=', 1)
            )
            ->select(['slug', 'excerpt', 'title', 'content']);
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['first_name', 'last_name'])
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
