<?php

declare(strict_types=1);

namespace EmzD\Workflow\Traits;

/**
 * Trait HasTablePrefix
 */
trait TablePrefix {
    public function getPrefix(): string
    {
        return config('emzd_workflow.table_prefix', '');
    }
}