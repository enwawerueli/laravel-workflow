<?php

declare(strict_types=1);

namespace EmzD\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use EmzD\Workflow\Traits\HasTablePrefix;

/**
 * Class Workflow
 */
class Event extends Model {
    use HasTablePrefix;

    protected $guarded = [];
}