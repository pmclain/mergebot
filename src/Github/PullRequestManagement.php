<?php
declare(strict_types=1);

namespace App\Github;

use App\Exception\HttpResponseException;

class PullRequestManagement
{
    const URL_MERGE = '/merge';
    const URL_FILES = '/files';

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
     * @param array $pullRequest
     * @throws HttpResponseException
     */
    public function merge(array $pullRequest): void
    {
        $this->comment($pullRequest, 'Auto-merged by mergebot.');
        $this->adapter->put($pullRequest['url'] . self::URL_MERGE);
    }

    /**
     * @param array $pullRequest
     * @param string $comment
     * @throws HttpResponseException
     */
    public function comment(array $pullRequest, string $comment): void
    {
        $this->adapter->post($pullRequest['comments_url'], [
            'body' => $comment,
        ]);
    }

    /**
     * @param array $pullRequest
     * @return array
     * @throws HttpResponseException
     */
    public function getFiles(array $pullRequest): array
    {
        return $this->adapter->get($pullRequest['url'] . self::URL_FILES);
    }
}
