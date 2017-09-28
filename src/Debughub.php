<?php

namespace Debughub\PhpClient;



class Debughub
{
    protected $config;
    protected $configPath;
    protected $logger;

    public function __construct($configPath = '', $logger = false)
    {
        $this->configPath = $configPath;
        $this->configure();
        if (!$logger) {
            $this->logger = new Logger($this->config);
        } elseif(is_a($logger, LoggerInterface::class)) {
            $this->logger = $logger;
        }
        $this->logger->boot();
    }

    private function configure()
    {
        // load the default config
        $defaultConfigPath = realpath(__DIR__.'/../config/debughub.php');
        $config = require($defaultConfigPath);

        // load the custom config
        $customConfig = [];
        if (file_exists($this->configPath)) {
            $customConfig = require($this->configPath);
        }
        // merge both configs - the custom one should override the default one
        foreach ($customConfig as $key => $value) {
            $config[$key] = $value;
        }

        // create config object
        $this->config = new Config();
        $this->config->setApiKey($config['api_key']);
        $this->config->setProjectKey($config['project_key']);
        $this->config->setEndpoint($config['endpoint']);
        $this->config->setGitRoot($config['git_root']);
        $this->config->setBlacklistParams($config['blacklist_params']);
        $this->config->setEnabled($config['enabled'] ? true : false);
        $this->config->setSendQueryData($config['send_query_data'] ? true : false);

    }

    public function route($route = '') {
        if ($this->config->getEnabled()) {
            $this->logger->requestHandler->route = $route;
        }
    }
    public function response($response = '') {
        if ($this->config->getEnabled()) {
            $this->logger->responseHandler->response = $route;
        }
    }
    public function query($query = '', $data = '', $duration = '', $connection = '') {
        if ($this->config->getEnabled()) {
            $this->logger->queryHandler->addQuery([
                'query' => $query,
                'data' => $data,
                'duration' => $duration,
                'connection' => $connection,
            ]);
        }
    }


    public function startQuery($query = '', $data = '', $duration = '', $connection = '') {
        if ($this->config->getEnabled()) {
            return $this->logger->queryHandler->addQuery([
                'query' => $query,
                'data' => $data,
                'duration' => $duration,
                'connection' => $connection,
            ]);
        }
        return 0;
    }
    public function endQuery($index = false) {
        if ($this->config->getEnabled()) {
            $this->logger->queryHandler->endQuery($index);
        }
    }

    public function log($data = '', $name = 'info'){
        if ($this->config->getEnabled()) {
            $this->logger->logHandler->addLog($data, $name);
        }
    }

    public function startLog($data = '', $name = 'info') {
        if ($this->config->getEnabled()) {
            return $this->logger->logHandler->addLog($data, $name);
        }
        return 0;
    }
    public function endLog($index = false) {
        if ($this->config->getEnabled()) {
            return $this->logger->logHandler->endLog($index);

        }
    }
}

function microtimeFloat($time)
{
  list($usec, $sec) = explode(" ", $time);
  return ((float)$usec + (float)$sec);
}
