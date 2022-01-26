<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Person extends Model
{
    use HasFactory;
    use HasSlug;

    protected $fillable = [
        'first_name',
        'last_name',
        'photo_url',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['full_name', 'parent_ids'];

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

    public function getParentIdsAttribute()
    {
        return $this->parents->modelKeys();
    }

    /**
     * Return s3 url of path if full path doesn't exist.
     */
    public function getPhotoUrlAttribute($value): string
    {
        if (empty($value)) {
            return $this->defaultPhotoUrl();
        }

        if (Str::startsWith($value, 'https://')) {
            return $value;
        }

        return Storage::url($value);
    }

    public function obituary(): HasOne
    {
        return $this->hasOne(Obituary::class)
            ->select(['id', 'person_id', 'birth_date', 'death_date', 'content', 'background_photo_url']);
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

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'parent_child', 'child_id', 'parent_id');
    }

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'parent_child', 'parent_id', 'child_id');
    }

    /**
     * if user is authenticated, return all people for use in tagging
     */
    public static function allForTagging(): array|Collection
    {
        return auth()->user() ? self::all()->map(fn ($person) => [
            'id' => $person->id,
            'full_name' => $person->full_name,
        ]) : [];
    }

    protected function defaultPhotoUrl(): string
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($this->full_name).'&color=374151&background=F3F4F6';
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
