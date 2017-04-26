<?php
declare(strict_types=1);

namespace padavvan\cshook;

use Composer\Script\Event;

/**
 * Class Composer
 * @package padavvan\cshook
 */
class Composer
{
    /**
     * @param Event $event
     */
    public static function postUpdate(Event $event)
    {
        $appDir = getcwd();
        $gitDir = $appDir . '/.git';

        $io = $event->getIO();
        $io->write($appDir);

        $installer = new Installer();
        $installer->installConfig($appDir);
        $installer->installHook($gitDir);

        Composer::logIt('Post update');
    }

    /**
     * @param $msg
     */
    public static function logIt($msg)
    {
        $handle = fopen('cshook.log', "a+");
        fwrite($handle, sprintf('%s - %s', date("Y-m-d H:i:s"), $msg));
        fclose($handle);
    }
}
