<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ActivityLoggerInterface
{
    public function log(
        string $module,
        string $action,
        Model $model,
        array $old = [],
        array $new = [],
        ?string $description = null
    ): void;
}