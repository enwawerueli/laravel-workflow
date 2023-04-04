<?php

declare(strict_types=1);

namespace EmzD\Workflow\Traits;

use Illuminate\Support\Str;

/**
 * Trait HasTablePrefix
 */
trait HasTablePrefix {
    use TablePrefix;

    public function getTable()
    {
        return $this->table ?? $this->getPrefix() . Str::snake(Str::pluralStudly(class_basename($this)));
    }

    public static function getTableName(): string
    {
        return (new static())->getTable();
    }
}