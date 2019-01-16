<?php
declare(strict_types=1);

namespace App\Github;

use App\Exception\HttpResponseException;

class ReleaseRepository
{
    const PATTERN_RELEASE_URL = 'https://api.github.com/repos/%s/%s/releases';
    const PATTERN_COMPARE_URL = 'https://api.github.com/repos/%s/%s/compare/%s...%s';

    /**
     * @var Adapter
     */
    private $adapter;

    public function __construct(
        Adapter $adapter
    ) {
        $this->adapter = $adapter;
    }

    /**
     * @param string $user
     * @param string $repo
     * @return array|null
     */
    public function getLatest(string $user, string $repo): ?array
    {
        try {
            $release = $this->adapter->get(sprintf(self::PATTERN_RELEASE_URL, $user, $repo) . '/latest');
        } catch (HttpResponseException $e) {
            $release = null;
        }

        return $release;
    }

    /**
     * @param string $user
     * @param string $repo
     * @param string $base
     * @param string $current
     * @return array
     * @throws HttpResponseException
     */
    public function getComparison(
        string $user,
        string $repo,
        string $base,
        string $current
    ): array {
        return $this->adapter->get(
            sprintf(self::PATTERN_COMPARE_URL, $user, $repo, $base, $current)
        );
    }

    /**
     * @param string $user
     * @param string $repo
     * @param string $target
     * @param string $tag
     * @param string $body
     * @return array
     * @throws HttpResponseException
     */
    public function create(
        string $user,
        string $repo,
        string $target,
        string $tag,
        string $body
    ): array {
        return $this->adapter->post(
            sprintf(self::PATTERN_RELEASE_URL, $user, $repo),
            [
                'tag_name' => $tag,
                'target_commitish' => $target,
                'name' => $tag,
                'body' => $body,
            ]
        );
    }
}
