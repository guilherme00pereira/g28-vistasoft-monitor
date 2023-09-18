<?php
/*
Plugin Name: G28 VistaSoft Monitor
Description: Monitoramento de imóveis da VistaSoft
Version: 0.1
Author: G28 - Guilherme Pereira
*/

if ( ! defined( 'ABSPATH' ) ) exit;

require "vendor/autoload.php";

use function G28\VistasoftMonitor\runPlugin;

runPlugin( __FILE__ );