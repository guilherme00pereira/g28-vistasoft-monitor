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

	}

	public function register()
	{
		add_action( 'g28_vistasoft_cron_hook', [ $this, 'execute' ] );
		add_filter( 'cron_schedules', [ $this, 'g28AddCronInterval' ] );
	}

	public function execute()
	{
		$api = new Client(Logger::LOGCRON);
		$api->listRealStates();
	}

	public function activate()
	{
		if ( !wp_next_scheduled( 'g28_vistasoft_cron_hook' ) ) {
			wp_schedule_event( time(), 'twenty_minutes', 'g28_vistasoft_cron_hook' );

		}
	}

	public function deactivate() {
		wp_clear_scheduled_hook( 'g28_vistasoft_cron_hook' );
	}

	public function g28AddCronInterval( $schedules )
	{
		$schedules['twenty_minutes'] = array(
			'interval' => 1200,
			'display'  => esc_html__( 'Every Twenty Minutes' ), );
		return $schedules;
	}
}