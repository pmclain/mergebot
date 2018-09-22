<?php

namespace App\ActionHandler\Github\PullRequest\Opened;

use App\ActionHandler\Github\PullRequest\Opened\MergeCondition\MergeConditionInterface;
use App\ActionHandler\TaskInterface;
use App\Exception\HttpResponseException;
use App\Model\Github\PullRequestManagement;

class AutoMerge implements TaskInterface
{
    /**
     * @var MergeConditionInterface[]
     */
    private $mergeConditions;

    /**
     * @var PullRequestManagement
     */
    private $pullRequestManagement;

    public function __construct(
        array $mergeConditions,
        PullRequestManagement $pullRequestManagement
    ) {
        $this->mergeConditions = $mergeConditions;
        $this->pullRequestManagement = $pullRequestManagement;
    }

    public function execute(array $data)
    {
        foreach ($this->mergeConditions as $mergeCondition) {
            if ($mergeCondition->allowMerge($data)) {
                try {
                    $this->pullRequestManagement->merge($data['pull_request']);
                } catch (HttpResponseException $e) {
                    // TODO: add some logging. until then... keep on truckin'
                }

                return;
            }
        }
    }
}
