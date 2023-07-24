<?php

namespace App\Providers;

use DateTime;
use DirectoryIterator;
use Illuminate\Support\ServiceProvider;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

class LogServiceProvider extends ServiceProvider
{
    /**
     * Configure logging on boot.
     *
     * @return void
     */
    public function boot()
    {
        $logLevel = env('LOG_LEVEL', 100);
        $logPath = storage_path('logs/lumen.log');
        $maxFiles = 30;

        $this->rotate($maxFiles, storage_path('logs'));

        $handlers[] = (new StreamHandler($logPath, $logLevel))->setFormatter(new LineFormatter(null, 'Y-m-d H:i:s', true, true));

        $this->app['log']->setHandlers($handlers);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    private function rotate($maxFiles, $path)
    {

        $datetime = new DateTime();
        $logOldName = 'lumen-' . $datetime->modify('-1 day')->format('d-m-Y') . '.log';

        $logPathNameNew = $path . '/lumen.log';
        $logPathNameOld = $path . '/' . $logOldName;

        if (!in_array($logOldName, scandir($path))) {
            if (file_exists($logPathNameNew)) {
                // Rename lumen.log to lumen-{date}.log
                rename($logPathNameNew, $logPathNameOld);
            }
            // create new lumen.log
            file_put_contents($logPathNameNew, '');
            chmod($logPathNameNew, 0777);
        }

        if (file_exists($path)) {
            foreach (new DirectoryIterator($path) as $fileInfo) {

                // ignore files
                if ($fileInfo->isDot() or $fileInfo->getFileName() == '.gitignore' or $fileInfo->getFileInfo()->isDir()) {continue;}

                $datetime = new DateTime();
                if ($datetime->modify('-' . $maxFiles . '  day')->getTimestamp() > filemtime($fileInfo->getRealPath())) {
                    unlink($fileInfo->getRealPath());
                }
            }
        }
    }
}
