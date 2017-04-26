<?php

namespace padavvan\cshook\interfaces;

/**
 * Interface FilterInterface
 * @package padavvan\cshook\interfaces
 */
interface FilterInterface
{
    /**
     * FilterInterface constructor.
     * @param $config
     */
    public function __construct($config);

    public function run();

    public function isOk();

    public function printErrors();
}
