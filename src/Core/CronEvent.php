<?php

namespace G28\VistasoftMonitor\Core;

use G28\VistasoftMonitor\VistaSoft\Client;

class CronEvent
{
    protected static ?CronEvent $_instance = null;

	public static function getInstance(): ?CronEvent {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		add_action( 'g28_vistasoft_cron_hook', [ $this, 'execute' ] );
	}

	public function execute()
	{
		$api = new Client();
		$api->listRealStates();
	}

	public function activate()
	{
		if ( !wp_next_scheduled( 'g28_vistasoft_cron_hook' ) ) {
			wp_schedule_event( time(), 'daily', 'g28_vistasoft_cron_hook' );
		}
	}

	public function deactivate() {
		wp_clear_scheduled_hook( 'g28_vistasoft_cron_hook' );
	}
}