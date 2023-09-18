<?php

namespace G28\VistasoftMonitor\Core;

class Logger
{
    protected static ?Logger $_instance = null;
    private string $file;
    private string $actualFilename;

    public function __construct()
    {
        $this->actualFilename   = "log_" . date("Ymd") . ".txt";
        $this->file             = Plugin::getDir() . 'logs/' . $this->actualFilename;
    }

    public static function getInstance(): ?Logger {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function add( string $message ) {
        date_default_timezone_set('America/Sao_Paulo');
        $timestamp    = date('d/m/Y h:i:s A');
        $output = "[ $timestamp ] $message" . PHP_EOL;
        file_put_contents( $this->file, $output, FILE_APPEND);
    }

	public function clear(  )
	{
		file_put_contents( $this->file, "" );
	}

    public function getLogFiles()
    {
        return array_diff( scandir(Plugin::getDir() . 'logs', SCANDIR_SORT_DESCENDING), array('.', '..'));
    }

    public function getLogFileContent( $file ): array
    {
        $filepath = Plugin::getDir() . 'logs/' . $file;
        return [ $file, nl2br(file_get_contents( $filepath )) ];
    }
}