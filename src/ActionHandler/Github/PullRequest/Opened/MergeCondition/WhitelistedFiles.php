<?php

namespace App\ActionHandler\Github\PullRequest\Opened\MergeCondition;

use App\Model\Github\PullRequestManagement;

class WhitelistedFiles implements MergeConditionInterface
{
    const PATTERN_NAME_ACTION = '%s_%s';

    /**
     * @var array
     */
    private $filelist;

    /**
     * @var PullRequestManagement
     */
    private $pullRequestManagement;

    public function __construct(
        PullRequestManagement $pullRequestManagement,
        array $filelist = []
    ) {
        $this->filelist = $filelist;
        $this->pullRequestManagement = $pullRequestManagement;
    }

    public function allowMerge(array $data): bool
    {
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
}
