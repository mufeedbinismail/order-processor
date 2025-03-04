<?php

namespace App\Contracts;

use App\Models\TaskRecord;

interface Flowable
{
    /**
     * Handles the approval
     *
     * @param TaskRecord $taskRecord
     * @return void
     */
    public function approve(TaskRecord $taskRecord): void;

    /**
     * Handles the rejection
     *
     * @param TaskRecord $taskRecord
     * @return void
     */
    public function reject(TaskRecord $taskRecord): void;
}