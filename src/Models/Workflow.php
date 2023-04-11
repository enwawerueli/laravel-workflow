<?php

declare(strict_types=1);

namespace EmzD\Workflow\Models;

use EmzD\Workflow\WorkflowBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use EmzD\Workflow\Traits\HasTablePrefix;

/**
 * Class Workflow
 */
class Workflow extends Model {
    use HasTablePrefix;

    protected $fillable = [
        'name',
        'type',
        'supports',
        'marking_property',
        'active',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public static function booted()
    {
        parent::booted();
        static::created(function ($model) {
            $model->events()->sync(Event::all());
        });
    }

    public function places(): HasMany
    {
        return $this->hasMany(Place::class);
    }

    public function initialPlaces(): HasMany
    {
        return $this->places()->whereInitial(true);
    }

    public function transitions(): HasMany
    {
        return $this->hasMany(Transition::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, table: $this->getPrefix() . 'workflow_events');
    }

    public function singleState(): bool
    {
        return $this->type === 'state_machine';
    }

    public function validate(): void
    {
        $builder = new WorkflowBuilder();
        $definition = $builder->buildDefinition($this);
        $builder->validateDefinition($definition, $this->singleState(), $this->name);
    }
}