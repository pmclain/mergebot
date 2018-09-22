<?php

namespace App\Model\Github;

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
    public function merge(array $pullRequest)
    {
        $this->comment($pullRequest);
        $this->adapter->put($pullRequest['url'] . self::URL_MERGE);
    }

    /**
     * @param array $pullRequest
     * @throws HttpResponseException
     */
    public function comment(array $pullRequest)
    {
        $this->adapter->post($pullRequest['comments_url'], [
            'body' => 'Auto-merged by mergebot.',
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
