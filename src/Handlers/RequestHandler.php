<?php

namespace Debughub\PhpClient\Handlers;

use Debughub\PhpClient\Reportable;
use Debughub\PhpClient\Config;


class RequestHandler implements Reportable
{
    public $params;
    public $headers;
    public $method;
    public $route;
    public $url;
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    private function filterParams()
    {
        $this->getParams();
        if (is_array($this->params)) {
            foreach ($this->params as $name => $param) {
                if (in_array($name, $this->config->getBlacklistParams())) {
                    $this->params[$name] = 'blacklisted param';
                }
            }
        }
    }

    private function getParams()
    {
        $this->params = array_merge($_GET, $_POST);
    }

    public function getData()
    {
        $this->filterParams();
        return [
            'params' => $this->params,
            'headers' => getallheaders(),
            'method' => strtolower($_SERVER['REQUEST_METHOD']),
            'route' => $this->route,
            'url' => strtolower($_SERVER['HTTP_HOST']) . strtolower($_SERVER['REQUEST_URI']),
        ];
    }
}
