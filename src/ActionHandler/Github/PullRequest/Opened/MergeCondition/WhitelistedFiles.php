<?php
declare(strict_types=1);

namespace App\ActionHandler\Github\PullRequest\Opened\MergeCondition;

use App\Github\ConfigRepository;
use App\Github\PullRequestManagement;

class WhitelistedFiles implements MergeConditionInterface
{
    /* Example config yml
        pullRequest:
          opened:
            autoMerge:
              mergeCondition:
                whitelistedFiles:
                  allowedFiles:
                    'composer.lock':
                      - modified
                    'yarn.lock':
                      - modified
                    'package.lock':
                      - modified
                    '.gitignore':
                      - modified
                      - added
     */
    const CONFIG_PATH_FILELIST = 'pullRequest/opened/autoMerge/mergeCondition/whitelistedFiles/allowedFiles';
    const PATTERN_NAME_ACTION = '%s_%s';

    /**
     * @var array
     */
    private $filelist = [];

    /**
     * @var PullRequestManagement
     */
    private $pullRequestManagement;

    /**
     * @var ConfigRepository
     */
    private $configRepository;

    public function __construct(
        PullRequestManagement $pullRequestManagement,
        ConfigRepository $configRepository
    ) {
        $this->pullRequestManagement = $pullRequestManagement;
        $this->configRepository = $configRepository;
    }

    public function allowMerge(array $data): bool
    {
        $this->initFilelist($data);

        if ($data['pull_request']['changed_files'] > count($this->filelist)) {
            return false;
        }

        return !count(array_diff(
            $this->concatPRNameAndAction($this->pullRequestManagement->getFiles($data['pull_request'])),
            $this->concatAllowedNamesAndActions()
        ));
    }

    private function concatPRNameAndAction(array $files): array
    {
        $result = [];
        foreach ($files as $file) {
            $result[] = sprintf(self::PATTERN_NAME_ACTION, $file['filename'], $file['status']);
        }

        return $result;
    }

    private function concatAllowedNamesAndActions(): array
    {
        $result = [];
        foreach ($this->filelist as $filename => $actions) {
            foreach ($actions as $action) {
                $result[] = sprintf(self::PATTERN_NAME_ACTION, $filename, $action);
            }
        }

        return $result;
    }

    private function initFilelist(array $data): void
    {
        $config = $this->configRepository->getConfig(
            $data['pull_request']['base']['repo']['owner']['login'],
            $data['pull_request']['base']['repo']['name'],
            $data['pull_request']['base']['ref']
        );

        $this->filelist = $config->getValue(self::CONFIG_PATH_FILELIST) ?? [];
    }
}
