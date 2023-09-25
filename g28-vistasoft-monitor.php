<?php
/*
Plugin Name: G28 VistaSoft Monitor
Description: Monitoramento de imóveis da VistaSoft
Version: 1.0.0
Author: G28 - Guilherme Pereira
Namespace: G28\VistasoftMonitor
*/

if ( ! defined( 'ABSPATH' ) ) exit;

require "vendor/autoload.php";

use function G28\VistasoftMonitor\runPlugin;

runPlugin( __FILE__ );