<?php

namespace Debughub\PhpClient\Handlers;

use Debughub\PhpClient\Reportable;

class QueryHandler implements Reportable
{
    public $queries = [];

    public function addQuery($data) {
        $data = [
          'query' => $data['query'] ? $data['query'] : '',
          'data' => $data['data'] ? $data['data'] : '',
          'duration' => $data['duration'] ? $data['duration'] : '',
          'start_time' => microtime(),
          'end_time' => microtime(),
          'connection' => $data['connection'] ? $data['connection'] : '',
        ];
        $this->queries[] = $data;
        return count($this->queries) - 1;
    }

    public function endQuery($index = null) {
        // if index is provided, get the item with key of the index. If not, get the last query
        if ($index === null) {
            $index = count($this->queries) - 1;
        }
        if (isset($this->queries[$index])) {
            $this->queries[$index]['end_time'] = microtime();
            $this->queries[$index]['duration'] = Debughub\PhpClient\microtimeFloat($this->queries[$index]['end_time']) - Debughub\PhpClient\microtimeFloat($this->queries[$index]['start_time']);
        }
    }

    public function getData()
    {
        foreach ($this->queries as $key => $value) {
            unset($this->queries[$key]['start_time']);
        }
        return $this->queries;
    }
}
