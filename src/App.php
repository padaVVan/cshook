<?php

namespace padavvan\cshook;

use padavvan\cshook\interfaces\FilterInterface;

/**
 * Class App
 * @package padavvan\cshook
 */
class App
{
    private $hooks = [
        'pre-commit' => [],
    ];

    private $config = [];

    private $filters = [
        'phpcs' => '\padavvan\cshook\filters\PhpcsFilter',
    ];

    /**
     * App constructor.
     * @param array $configFiles
     */
    public function __construct(array $configFiles = [])
    {
        $this->loadConfigFromFile(__DIR__ . '\assets\.cshook');
        array_map([$this, 'loadConfigFromFile'], $configFiles);
        $this->initialize();
    }

    /**
     * @param $filename
     */
    private function loadConfigFromFile($filename)
    {
        if (is_file($filename) && is_readable($filename)) {
            $json = file_get_contents($filename);
            $this->config = array_merge_recursive($this->config, (array)json_decode($json, true));
        }
    }

    private function initialize()
    {
        foreach ($this->config as $key => $value) {
            if (isset($this->hooks[$key])) {
                $this->hooks[$key] = $this->buildFilters($value);
            }
        }
    }

    /**
     * @param $config
     * @return array
     */
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

    /**
     * @param $key
     * @param array $config
     * @return mixed
     * @throws \Exception
     */
    private function buildFilter($key, $config = [])
    {
        $filter = isset($this->filters[$key]) ? $this->filters[$key] : null;

        if ($filter === null) {
            throw new \Exception('Filter not found');
        }

        return new $filter($config);
    }

    /**
     * @param $key
     * @return array|mixed
     */
    private function getConfig($key)
    {
        return isset($this->config[$key]) ? $this->config[$key] : [];
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
}
