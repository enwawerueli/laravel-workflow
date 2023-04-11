<?php

declare(strict_types=1);

namespace EmzD\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use EmzD\Workflow\Traits\HasTablePrefix;

/**
 * Class Workflow
 */
class Transition extends Model {
    use HasTablePrefix;

    protected $fillable = [
        'name',
        'workflow_id',
        'guard',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    public function from(): BelongsToMany
    {
        return $this->belongsToMany(Place::class, table: $this->getPrefix() . 'from_place_transition', relatedPivotKey: 'from_id');
    }

    public function to(): BelongsToMany
    {
        return $this->belongsToMany(Place::class, table: $this->getPrefix() . 'to_place_transition', relatedPivotKey: 'to_id');
    }
}