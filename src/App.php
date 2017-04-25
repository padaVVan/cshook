<?php

namespace padavvan\cshook;

use padavvan\cshook\interfaces\FilterInterface;

class App
{
    private $hooks = [
        'pre-commit' => [],
    ];

    private $config = [];

    private $filters = [
        'phpcs' => '\padavvan\cshook\filters\PhpcsFilter',
    ];

    public function __construct($config = [])
    {
        $json = file_get_contents(__DIR__ . '\assets\.cshook');
        $this->config = json_decode($json, true);

        $this->initialize();
    }

    /**
     * @param $hook
     * @return int
     */
    public function run($hook)
    {
        $hook = isset($this->hooks[$hook]) ? $this->hooks[$hook] : null;

        if ($hook === null) {
            return 0;
        }

        /** @var FilterInterface $filter */
        foreach ($hook as $filter) {
            $filter->run();

            if (!$filter->isOk()) {
                $filter->printErrors();
                return 1;
            }
            
        }
        return 0;
    }

    private function initialize()
    {
        foreach ($this->config as $key => $value) {
            if (isset($this->hooks[$key])) {
                $this->hooks[$key] = $this->buildFilters($value);
            }
        }
    }

    private function buildFilters($config)
    {
        $filters = [];
        foreach ($config as $key => $value) {
            if (is_int($key)) {
                $filters[] = $this->buildFilter($value, $this->getConfig($value));
            } else {
                $filters[] = $this->buildFilter($key, array_merge($this->getConfig($value), $value));
            }
        }
        return $filters;
    }

    private function getConfig($key)
    {
        return isset($this->config[$key]) ? $this->config[$key] : [];
    }

    private function buildFilter($key, $config = [])
    {
        $filter = isset($this->filters[$key]) ? $this->filters[$key] : null;

        if ($filter === null) {
            throw new \Exception('Filter not found');
        }

        return new $filter($config);
    }
}
