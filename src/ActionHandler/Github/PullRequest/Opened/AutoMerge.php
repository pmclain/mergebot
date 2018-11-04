<?php

namespace App\ActionHandler\Github\PullRequest\Opened;

use App\ActionHandler\PermissionValidator;
use App\ActionHandler\Github\PullRequest\Opened\MergeCondition\MergeConditionInterface;
use App\ActionHandler\TaskInterface;
use App\Exception\HttpResponseException;
use App\Github\ConfigRepository;
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

    /**
     * @var ConfigRepository
     */
    private $configRepository;

    /**
     * @var PermissionValidator
     */
    private $permissionValidator;

    public function __construct(
        array $mergeConditions,
        PullRequestManagement $pullRequestManagement,
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        PermissionValidator $permissionValidator
    ) {
        $this->mergeConditions = $mergeConditions;
        $this->pullRequestManagement = $pullRequestManagement;
        $this->logger = $logger;
        $this->configRepository = $configRepository;
        $this->permissionValidator = $permissionValidator;
    }

    public function execute(array $data)
    {
        if (!$this->isAllow($data)) {
            return;
        }

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

    /**
     * @param array $data
     * @return bool
     */
    private function isAllow(array $data): bool
    {
        $config = $this->configRepository->getConfig(
            $data['pull_request']['base']['repo']['owner']['login'],
            $data['pull_request']['base']['repo']['name'],
            $data['pull_request']['base']['ref']
        );

        return $this->permissionValidator->isAllowAction(get_class($this), $config);
    }
}
