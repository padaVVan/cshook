<?php

namespace padavvan\cshook\common;

/**
 * Class Binary
 * @package padavvan\cshook
 */
class Binary
{
    private $bin;

    private $params;

    private $flags;

    /**
     * Binary constructor.
     * @param $bin
     * @param null $params
     * @param null $flags
     */
    public function __construct($bin, $params = null, $flags = null) {
        $this->bin = $bin;
        $this->params = $params;
        $this->flags = $flags;
    }

    /**
     * @return array
     */
    public function exec() {
        $flags = $this->prepareFlags($this->flags);
        $params = $this->prepareParams($this->params);

        $cmd = sprintf('%s %s %s', $this->bin, $params, $flags);
        $result = [];
        exec($cmd, $result);
        return $result;
    }

    /**
     * @param $flags
     * @return string
     */
    private function prepareFlags($flags) {
        if (is_string($flags)) {
            return $flags;
        } elseif (is_array($flags)) {
            $arr = [];
            foreach ($flags as $key => $value) {
                if (is_array($value)) {
                    $value = implode(',', $value);
                    $arr[] = sprintf('%s=%s', $key, $value);
                } elseif ($value === null) {
                    $arr[] = sprintf('%s', $key);
                } elseif (is_int($key)) {
                    $arr[] = sprintf('%s', $value);
                } else {
                    $arr[] = sprintf('%s=%s', $key, $value);
                }
            }
            return implode(' ', $arr);
        }
        return $flags;
    }

    /**
     * @param $params
     * @return string
     */
    private function prepareParams($params)
    {
        if (is_string($params)) {
            return $params;
        } elseif (is_array($params)) {
            return implode(' ', $params);
        }
        return $params;
    }

    /**
     * @param $key
     * @param $value
     */
    public function addFlag($key, $value)
    {
        $this->flags[$key] = $value;
    }

    /**
     * @param $values
     */
    public function setParams($values)
    {
        $this->params = $values;
    }
}
