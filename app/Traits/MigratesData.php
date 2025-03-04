<?php

namespace App\Traits;

trait MigratesData
{
    public function migrateData(callable $callback)
    {
        try {
            $callback();
        }

        catch (\Exception $e) {
            $this->down();
            throw $e;
        }
    }
}