<?php

namespace App\Github\PullRequest;

use App\Github\Adapter;

class FilesRepository
{
    /**
     * @var array
     */
    private $filesByUrl = [];

    /**
     * @var Adapter
     */
    private $adapter;

    public function __construct(
        Adapter $adapter
    ) {
        $this->adapter = $adapter;
    }

    public function get(string $url): array
    {
        if (isset($this->filesByUrl[$url])) {
            return $this->filesByUrl[$url];
        }

        $this->filesByUrl[$url] = $this->adapter->get($url);

        return $this->filesByUrl[$url];
    }
}
