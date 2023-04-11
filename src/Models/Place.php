<?php

declare(strict_types=1);

namespace EmzD\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use EmzD\Workflow\Traits\HasTablePrefix;

/**
 * Class Workflow
 */
class Place extends Model {
    use HasTablePrefix;

    protected $fillable = [
        'name',
        'workflow_id',
        'initial',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }
}