<?php

namespace App\ActionHandler\Github\PullRequest\Opened\MergeCondition;

interface MergeConditionInterface
{
    public function allowMerge(array $data): bool;
}
