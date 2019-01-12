<?php

namespace App\ActionHandler\Github\PullRequest\Closed;

use App\ActionHandler\Config;
use App\ActionHandler\PermissionValidator;
use App\ActionHandler\TaskInterface;
use App\Exception\HttpResponseException;
use App\Github\ConfigRepository;
use App\Github\PullRequestManagement;
use App\Github\ReleaseRepository;
use Psr\Log\LoggerInterface;

class ReleaseMe implements TaskInterface
{
    /* Example config yml
    pullRequest:
      closed:
        releaseMe:
          targetBranch: 'master'
    */
    const CONFIG_PATH_TARGET_BRANCH = 'pullRequest/closed/releaseMe/targetBranch';
    const RELEASE_BODY_TRIGGER = 'ReleaseMe';

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

    /**
     * @var ReleaseRepository
     */
    private $releaseRepository;

    /**
     * @var PullRequestManagement
     */
    private $pullRequestManagement;

    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        PermissionValidator $permissionValidator,
        ReleaseRepository $releaseRepository,
        PullRequestManagement $pullRequestManagement
    ) {
        $this->logger = $logger;
        $this->configRepository = $configRepository;
        $this->permissionValidator = $permissionValidator;
        $this->releaseRepository = $releaseRepository;
        $this->pullRequestManagement = $pullRequestManagement;
    }

    public function execute(array $data)
    {
        if (!$this->shouldCreateRelease($data)) {
            return;
        }

        $login = $this->getLogin($data);
        $repo = $this->getRepoName($data);
        $refName = $this->getRefName($data);

        $latest = $this->releaseRepository->getLatest($login, $repo);

        if (!$latest) {
            return;
        }

        try {
            $compare = $this->releaseRepository->getComparison($login, $repo, $latest['tag_name'], $refName);
            $newTag = $this->extractNewTag($data['pull_request']['body']);
            $releaseBody = $this->createReleaseBody($compare);

            $release = $this->releaseRepository->create($login, $repo, $refName, $newTag, $releaseBody);
            $this->pullRequestManagement->comment(
                $data['pull_request'],
                sprintf('Release %s created %s', $newTag, $release['html_url'])
            );
        } catch (HttpResponseException $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @param string $body
     * @return string
     */
    private function extractNewTag(string $body): string
    {
        preg_match(sprintf('~%s\s([^\s]+)~', self::RELEASE_BODY_TRIGGER), $body, $matches);
        return $matches[1];
    }

    /**
     * @param array $compare
     * @return string
     */
    private function createReleaseBody(array $compare): string
    {
        $body = '';
        foreach ($compare['commits'] as $commit) {
            // WTF github why so many keys? *sigh* i know graphql makes it right
            if (strpos($commit['commit']['message'], 'Merge pull request') !== false) {
                continue;
            }
            $body .= '* ' . rtrim((string) strtok($commit['commit']['message'], "\n")) . "\r\n";
        }
        $body .= "\r\nRelease created by mergebot";

        return $body;
    }

    /**
     * @param array $data
     * @return bool
     */
    private function shouldCreateRelease(array $data): bool
    {
        $config = $this->getConfig($data);
        $createRelease = true;
        $message = '';

        if (!$this->permissionValidator->isAllowAction(get_class($this), $config)) {
            $createRelease = false;
            $message = 'ReleaseMe is not configured for this PR.';
        } elseif (!$data['pull_request']['merged']) {
            $createRelease = false;
            $message = 'PR was not merged.';
        } elseif ($config->getValue(self::CONFIG_PATH_TARGET_BRANCH) !== $data['pull_request']['base']['ref']) {
            $createRelease = false;
            $message = 'PR target is not ReleaseMe target.';
        } elseif (stripos($data['pull_request']['body'], self::RELEASE_BODY_TRIGGER) === false) {
            $createRelease = false;
            $message = 'Trigger not found in PR body.';
        }

        if (!$createRelease) {
            //TODO: log the message
        }

        return $createRelease;
    }

    /**
     * @param array $data
     * @return Config
     */
    private function getConfig(array $data): Config
    {
        return $this->configRepository->getConfig(
            $this->getLogin($data),
            $this->getRepoName($data),
            $this->getRefName($data)
        );
    }

    private function getLogin(array $data): string
    {
        return $data['pull_request']['base']['repo']['owner']['login'];
    }

    private function getRepoName(array $data): string
    {
        return $data['pull_request']['base']['repo']['name'];
    }

    private function getRefName(array $data): string
    {
        return $data['pull_request']['base']['ref'];
    }
}
