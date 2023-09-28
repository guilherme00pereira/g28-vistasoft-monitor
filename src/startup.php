<?php

namespace G28\VistasoftMonitor;


use G28\VistasoftMonitor\Core\CronEvent;
use G28\VistasoftMonitor\Core\OptionManager;
use G28\VistasoftMonitor\Core\Plugin;

if( !function_exists( __NAMESPACE__ . 'runPlugin') )
{
    function runPlugin( $root )
    {
        add_action( 'plugins_loaded', function () use ( $root ) {
	        Plugin::getInstance( $root );
	        add_filter( 'plugin_action_links_' . Plugin::getPluginBase(), __NAMESPACE__ . '\settings_link' );
            new Controller();
			new OptionManager();
			CronEvent::getInstance()->register();
        } );
    }
}

if( !function_exists( __NAMESPACE__ . 'settings_link') )
{
	function settings_link( $links ): array {
		$plugin_links   = array();
		$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=28-vistasoft-monitor' ) ) . '">' . __( 'Settings', Plugin::getTextDomain() ) . '</a>';
		return array_merge( $plugin_links, $links );
	}
}
