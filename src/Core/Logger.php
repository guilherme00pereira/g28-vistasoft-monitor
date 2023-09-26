<?php

namespace G28\VistasoftMonitor\Core;

class Logger
{
    protected static ?Logger $_instance = null;
	private string $logProcessFile;
    private string $logResumeFile;

	public function __construct()
    {
	    $this->logProcessFile = "log_" . date("Ymd") . ".txt";
        $this->logResumeFile  = "log_resume_" . date("Ymd") . ".txt";
		if(!file_exists(Plugin::getLogDir())) {
			mkdir(Plugin::getLogDir());
		}
		if(!file_exists(Plugin::getLogDir() . $this->logProcessFile)) {
			file_put_contents(Plugin::getLogDir() . $this->logProcessFile, "");
		}
        if(!file_exists(Plugin::getLogDir() . $this->logResumeFile)) {
            file_put_contents(Plugin::getLogDir() . $this->logResumeFile, "");
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
		$actualOutput = file_get_contents( Plugin::getLogDir() . $this->logProcessFile );
        $output = "[ $timestamp ] $message" . PHP_EOL . $actualOutput;
        file_put_contents( Plugin::getLogDir() . $this->logProcessFile, $output);
    }

	public function clear(  ) {
		file_put_contents( Plugin::getLogDir() . $this->logProcessFile, "");
        file_put_contents(Plugin::getLogDir() . $this->logResumeFile, "");
	}

    public function getLogProcessFileContent(): string
    {
        $filepath = Plugin::getLogDir() . $this->logProcessFile;
        return nl2br(file_get_contents( $filepath ));
    }

    public function getLogResumeFileContent(): string
    {
        $filepath = Plugin::getLogDir() . $this->logResumeFile;
        return nl2br(file_get_contents( $filepath ));
    }
}