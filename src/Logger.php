<?php

namespace Debughub\PhpClient;



class Logger implements LoggerInterface
{
    public $queryHandler;
    public $exceptionHandler;
    public $logHandler;
    public $requestHandler;
    public $responseHandler;
    public $startTime;
    public $endTime;
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->startTime = microtime();
    }

    public function boot()
    {
        if ($this->config->getEnabled()) {
            $this->logHandler = new Handlers\LogHandler();
            $this->queryHandler = new Handlers\QueryHandler();
            $this->exceptionHandler = new Handlers\ExceptionHandler();
            $this->requestHandler = new Handlers\RequestHandler($this->config);
            $this->responseHandler = new Handlers\ResponseHandler($this->config);
            $this->registerShutdown();

        }

    }


    public function registerShutdown()
    {
      register_shutdown_function(function(){
        $payload = $this->createPayload();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config->getEndpoint());
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec ($ch);
        curl_close ($ch);
      });
    }

    private function createPayload()
    {

        $endTime = microtime();
        $timeStartFloat = microtimeFloat($this->startTime);
        $timeEndFloat = microtimeFloat($endTime);
        $duration = $timeEndFloat - $_SERVER['REQUEST_TIME_FLOAT'];
        return [
          'data' =>[
              'boot_time' => $this->startTime,
              'start_time' => $_SERVER['REQUEST_TIME_FLOAT'],
              'end_time' => $endTime,
              'queries' => $this->queryHandler->getData(),
              'exceptions' => $this->exceptionHandler->getData(),
              'logs' => $this->logHandler->getData(),
              'request' => $this->requestHandler->getData(),
              'response' => $this->responseHandler->getData(),
              'duration' => $duration,
          ],
          'api_key' => $this->config->getApiKey(),
          'project_key' => $this->config->getProjectKey(),
        ];
    }
}
