<?php
declare(strict_types=1);

namespace App\Github;

use App\ActionHandler\Config;
use App\ActionHandler\ConfigFactory;
use Symfony\Component\Yaml\Yaml;

class ConfigRepository
{
    const CONFIG_URL_PATTERN = 'https://raw.githubusercontent.com/%s/%s/%s/.mergebot.yml';

    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * @var ConfigFactory
     */
    private $configFactory;

    /**
     * @var Config[]
     */
    private $configCache = [];

    public function __construct(
        Adapter $adapter,
        ConfigFactory $configFactory
    ) {
        $this->adapter = $adapter;
        $this->configFactory = $configFactory;
    }

    /**
     * @param string $user
     * @param string $repo
     * @param string $ref
     * @return Config
     */
    public function getConfig(string $user, string $repo, string $ref): Config
    {
        $configUrl = sprintf(self::CONFIG_URL_PATTERN, $user, $repo, $ref);
        if (isset($this->configCache[$configUrl])) {
            return $this->configCache[$configUrl];
        }

        return $this->loadConfig($configUrl);
    }

    /**
     * @param string $url
     * @return Config
     */
    private function loadConfig(string $url): Config
    {
        try {
            $yml = $this->adapter->getRaw($url);
            $config = Yaml::parse($yml);
        } catch (\Exception $e) {
            $config = [];
        }

        $this->configCache[$url] = $this->configFactory->create($config);
        return $this->configCache[$url];
    }
}
