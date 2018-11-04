<?php

namespace App\ActionHandler\Github\PullRequest\Opened;

use App\ActionHandler\Github\PullRequest\Opened\MergeCondition\MergeConditionInterface;
use App\ActionHandler\TaskInterface;
use App\Exception\HttpResponseException;
use App\Github\PullRequestManagement;
use Psr\Log\LoggerInterface;

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

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        array $mergeConditions,
        PullRequestManagement $pullRequestManagement,
        LoggerInterface $logger
    ) {
        $this->mergeConditions = $mergeConditions;
        $this->pullRequestManagement = $pullRequestManagement;
        $this->logger = $logger;
    }

    public function execute(array $data)
    {
        foreach ($this->mergeConditions as $mergeCondition) {
            if ($mergeCondition->allowMerge($data)) {
                try {
                    $this->pullRequestManagement->merge($data['pull_request']);
                    return;
                } catch (HttpResponseException $e) {
                    $this->logger->error($e);
                }
            }
        }
    }
}
