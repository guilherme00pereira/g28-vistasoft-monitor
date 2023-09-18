<?php

namespace G28\VistasoftMonitor;


if( !function_exists( __NAMESPACE__ . 'runPlugin') )
{
    function runPlugin( $root )
    {
        add_action( 'plugins_loaded', function () use ( $root ) {
            new Controller();
        } );
    }
}