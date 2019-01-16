<?php
declare(strict_types=1);

namespace App\ActionHandler\Github\PullRequest\Opened\MergeCondition;

interface MergeConditionInterface
{
    public function allowMerge(array $data): bool;
}
