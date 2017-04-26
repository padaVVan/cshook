<?php

namespace padavvan\cshook\filters;

use padavvan\cshook\common\Binary;

/**
 * Class PhpcsFilter
 */
class PhpcsFilter extends BaseFilter
{
    private $binary;

    private $result = [];

    private $exts = [
        'php', 'inc',
    ];

    /**
     * PhpcsFilter constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $flags = isset($config['flags']) ? $config['flags'] : [];
        $params = isset($config['params']) ? $config['params'] : [];
        $bin = isset($config['bin']) ? $config['bin'] : getcwd() . '/vendor/bin/phpcs';

        $this->binary = new Binary($bin, $params, $flags);
        $this->binary->addFlag('--report', 'CSV');
    }

    /**
     *
     */
    public function run()
    {
        $changeFiles = $this->getChangeFiles();
        $changeFiles = $this->filterPhpFiles($changeFiles);

        if ($changeFiles) {
            $this->binary->setParams($changeFiles);
            $this->result = $this->binary->exec();
        }
    }

    /**
     * @return array
     */
    private function getChangeFiles()
    {
        $binary = new Binary('git', 'diff', ['--name-only', '--cached']);

        return $binary->exec();
    }

    /**
     * @param array $files
     * @return array
     */
    private function filterPhpFiles(array $files)
    {
        return array_filter($files, function ($file) {
            $ext = substr($file, strripos($file, '.') + 1);
            return in_array($ext, $this->exts);
        });
    }

    /**
     * @return bool
     */
    public function isOk()
    {
        return count($this->result) == 1;
    }

    /**
     *
     */
    public function printErrors()
    {
        array_shift($this->result);
        foreach ($this->result as $line) {
            $arr = str_getcsv($line);
            echo sprintf("%s [L-%s, C-%s]\n", $arr[0], $arr[1], $arr[2]);
        }
    }
}
