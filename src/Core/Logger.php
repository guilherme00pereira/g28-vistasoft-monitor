<?php

namespace G28\VistasoftMonitor\Core;

class Logger
{
    protected static ?Logger $_instance = null;
	private string $filename;

	public function __construct()
    {
	    $this->filename = "log_" . date("Ymd") . ".txt";
		if(!file_exists(Plugin::getLogDir())) {
			mkdir(Plugin::getLogDir());
		}
		if(!file_exists(Plugin::getLogDir() . $this->filename)) {
			file_put_contents(Plugin::getLogDir() . $this->filename, "");
		}
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
        $output = "[ $timestamp ] $message" . PHP_EOL . file_get_contents( $this->filename );
        file_put_contents( $this->filename, $output);
    }

    public function getLogFileContent(): array
    {
	    file_put_contents( $this->filename, "" );
        $filepath = Plugin::getLogDir() . $this->filename;
        return [ $this->filename, nl2br(file_get_contents( $filepath )) ];
    }
}