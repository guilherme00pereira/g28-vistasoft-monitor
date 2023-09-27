<?php

namespace G28\VistasoftMonitor;

use Exception;
use G28\VistasoftMonitor\Core\CronEvent;
use G28\VistasoftMonitor\Core\Logger;
use G28\VistasoftMonitor\Core\OptionManager;
use G28\VistasoftMonitor\Core\Plugin;

class Controller
{
	public function __construct()
	{
		add_action('admin_menu', array($this, 'addPage' ));
		add_action( 'admin_enqueue_scripts', [ $this, 'registerStylesAndScripts'] );
		add_action( 'wp_ajax_readLog', [ $this, 'readLog' ] );
		add_action('wp_ajax_toggleEnable', [ $this, 'toggleEnable' ] );
	}

	public function addPage()
	{
		add_menu_page(
			"VistaSoft",
			"VistaSoft",
			'manage_options',
			'g28-vistasoft-monitor',
			array( $this, 'renderPage' ),
			'dashicons-admin-generic',
			'2',
		);
	}

	public function renderPage(  )
	{
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_enqueue_style(Plugin::getAssetsPrefix() . 'admin_style');
		wp_enqueue_script( Plugin::getAssetsPrefix() . 'admin-scripts' );

		ob_start();
		include sprintf( "%sadmin-settings.php", Plugin::getTemplateDir() );
		$html = ob_get_clean();
		echo $html;
	}

	public function readLog()
	{
		try {
			$content    = Logger::getInstance()->getLogProcessFileContent();
			echo json_encode(['success' => true, 'message' => $content]);
		} catch (Exception $e) {
			echo json_encode(['error' => false, 'message' => 'Erro ao abrir arquivo de log.']);
		}
		wp_die();
	}

	public function toggleEnable()
	{
		try {
			$optionManager = new OptionManager();
			$optionManager->toggleEnable();
			$enable = $_POST['enable'];
			$enable === "1" ? CronEvent::getInstance()->activate() : CronEvent::getInstance()->deactivate();
			echo json_encode(['success' => true, 'message' => '']);
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
			'g28_vistasoft_monitor_nonce'	=> wp_create_nonce( 'g28_vistasoft_monitor_nonce' ),
			'action_ReadLog'                => 'readLog',
			'action_toggleEnable'			=> 'toggleEnable',
			'enabled'						=> ( new OptionManager() )->getEnable(),
		]);
	}

}