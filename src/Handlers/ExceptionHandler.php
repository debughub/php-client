<?php

namespace Debughub\Clients\Php\Handlers;

use Debughub\Clients\Php\Reportable;

class ExceptionHandler implements Reportable
{
    public $exceptions = [];

    public function getData()
    {
      return $this->exceptions;
    }
}
