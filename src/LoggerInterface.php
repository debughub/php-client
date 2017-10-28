<?php

namespace Debughub\Clients\Php;

interface LoggerInterface
{
    public function boot();
    public function registerShutdown();
}
