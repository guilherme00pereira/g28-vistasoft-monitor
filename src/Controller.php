<?php

namespace G28\VistasoftMonitor;

use Exception;
use G28\VistasoftMonitor\Api\VistaSoftClient;
use G28\VistasoftMonitor\Core\Logger;
use G28\VistasoftMonitor\Core\OptionManager;
use G28\VistasoftMonitor\Core\Plugin;

class Controller
{
	public function __construct()
	{
		add_action('admin_menu', array($this, 'addPage' ));
		add_action( 'admin_enqueue_scripts', [ $this, 'registerStylesAndScripts'] );
		add_action( 'wp_ajax_ajaxGetLog', [ $this, 'ajaxGetLog' ] );
	}

	public function addPage()
	{
		add_submenu_page(
			null,
			"VistaSoft",
			"VistaSoft",
			'manage_options',
			'integra-jetengine-vistasoft',
			array( $this, 'renderPage' )
		);
	}

	public function renderPage(  )
	{
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_enqueue_style(Plugin::getAssetsPrefix() . 'admin_style');
		wp_enqueue_script( Plugin::getAssetsPrefix() . 'admin-scripts' );

		$options = new OptionManager();
		$fields = $options->getFieldsMapping();
		$features = $options->getFeaturesMapping(); 

		ob_start();
		include sprintf( "%sadmin-settings.php", Plugin::getTemplateDir() );
		$html = ob_get_clean();
		echo $html;
	}

	public function ajaxGetLog()
	{
		try {
			$api = new VistaSoftClient();
			$api->listRealStates();
			wp_verify_nonce( 'g28_integra_jetvsoft_nonce' );
			$file	    = json_decode( stripslashes( $_GET['filename'] ) );
			$content    = Logger::getInstance()->getLogFileContent( $file );
			echo json_encode(['success' => true, 'message' => $content]);
		} catch (Exception $e) {
			echo json_encode(['error' => false, 'message' => 'Erro ao abrir arquivo de log.']);
		}
		wp_die();
	}

	public function registerStylesAndScripts()
	{
		wp_register_style( Plugin::getAssetsPrefix() . 'admin_style', Plugin::getAssetsUrl() . 'css/admin-settings.css' );
		wp_register_script(
			Plugin::getAssetsPrefix() . 'admin-scripts',
			Plugin::getAssetsUrl() . 'js/admin-settings.js',
			array( 'jquery' ),
			null,
			true
		);
		wp_localize_script( Plugin::getAssetsPrefix() . 'admin-scripts', 'ajaxobj', [
			'ajax_url'        	            => admin_url( 'admin-ajax.php' ),
			'g28_integra_jetvsoft_nonce'	=> wp_create_nonce( 'g28_integra_jetvsoft_nonce' ),
			'action_getLog'                 => 'ajaxGetLog',
		]);
	}

}