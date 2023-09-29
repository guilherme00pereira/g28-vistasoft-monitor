<?php

namespace G28\VistasoftMonitor\Core;

class Logger
{
	const LOGCRON = "cron";
	const LOGADD = "add";
	private string $logCronFile;
	private string $logAddFile;

	private string $logType;

	public function __construct($logType)
    {
		$this->logType = $logType;
	    $this->logCronFile = "cron_" . date("Ymd") . ".txt";
        $this->logAddFile  = "add_" . date("Ymd") . ".txt";
		if(!file_exists(Plugin::getLogDir())) {
			mkdir(Plugin::getLogDir());
		}
		if(!file_exists(Plugin::getLogDir() . $this->logCronFile)) {
			file_put_contents(Plugin::getLogDir() . $this->logCronFile, "");
		}
		if(!file_exists(Plugin::getLogDir() . $this->logAddFile)) {
			file_put_contents(Plugin::getLogDir() . $this->logAddFile, "");
		}
	}

    public function add( string $message ) {
		$file = $this->logType === self::LOGCRON ? $this->logCronFile : $this->logAddFile;
        date_default_timezone_set('America/Sao_Paulo');
        $timestamp    = date('d/m/Y h:i:s A');
		$actualOutput = file_get_contents( Plugin::getLogDir() . $file );
        $output = "[ $timestamp ] $message" . PHP_EOL . $actualOutput;
        file_put_contents( Plugin::getLogDir() . $file, $output);
    }

	public function clear(  ) {
		file_put_contents( Plugin::getLogDir() . $this->logCronFile, "");
		file_put_contents( Plugin::getLogDir() . $this->logAddFile, "");
	}

    public function getLogContent(): string
    {
		$file = $this->logType === self::LOGCRON ? $this->logCronFile : $this->logAddFile;
        $filepath = Plugin::getLogDir() . $file;
        return nl2br(file_get_contents( $filepath ));
    }
}