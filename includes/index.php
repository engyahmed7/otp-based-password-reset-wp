<?php

if (!defined('ABSPATH')) exit;


class AppExpert_File_Includer
{
    /**
     * Directory to start scanning for PHP files.
     *
     * @var string
     */
    private $baseDir;

    /**
     * Constructor.
     *
     * @param string $baseDir The base directory to scan.
     */
    public function __construct($baseDir)
    {
        $this->baseDir = $baseDir;
    }

    /**
     * Start including PHP files.
     *
     * @return void
     */
    public function includeAll()
    {
        $this->includePhpFiles($this->baseDir);
    }

    /**
     * Recursively include all PHP files in the directory and its subdirectories.
     *
     * @param string $dir The directory to scan for PHP files.
     * @return void
     */
    private function includePhpFiles($dir)
    {
        $files = glob($dir . '/*');

        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->includePhpFiles($file);
            } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                require_once $file;
            }
        }
    }
}

$fileIncluder = new AppExpert_File_Includer(__DIR__);
$fileIncluder->includeAll();
