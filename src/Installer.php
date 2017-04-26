<?php

namespace padavvan\cshook;

/**
 * Class Installer
 * @package padavvan\cshook
 */
class Installer
{
    /**
     * @param $gitPath
     */
    public function installHook($gitPath)
    {
        if (!is_dir($gitPath)) {
            return;
        }

        copy(__DIR__ . '/assets/pre-commit', $gitPath . '/hooks/git-hook.php');
    }

    /**
     * @param $applicationPath
     */
    public function installConfig($applicationPath)
    {
        copy(__DIR__ . '/assets/.cshook', $applicationPath . '/.cshook');
    }
}
