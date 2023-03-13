<?php

declare(strict_types=1);

namespace EmzD\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use EmzD\Workflow\Traits\HasTablePrefix;

/**
 * Class Workflow
 */
class Workflow extends Model {
    use HasTablePrefix;

    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function places(): HasMany
    {
        return $this->hasMany(Place::class);
    }

    public function initialPlaces(): HasMany
    {
        return $this->places()->whereIsInitial(true);
    }

    public function transitions(): HasMany
    {
        return $this->hasMany(Transition::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, table: $this->getPrefix() . 'workflow_events');
    }

    public function eventsToDispatch(): BelongsToMany
    {
        return $this->events()->wherePivot('enabled', true);
    }

    public static function booted()
    {
        parent::booted();
        static::created(function (Model $model) {
            if ($this->events->isEmpty()) {
                $this->events()->attach(Event::all());
            }
        });
    }
}