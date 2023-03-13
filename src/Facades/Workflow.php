<?php

declare(strict_types=1);

namespace EmzD\Workflow\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Workflow
 */
class Workflow extends Facade {
    protected static function getFacadeAccessor()
    {
        return \EmzD\Workflow\WorkflowServiceInterface::class;
    }
}