<?php

namespace G28\VistasoftMonitor\Core;

class Plugin {

	protected static ?Plugin $_instance = null;

	/**
	 * @var string
	 */
	private static string $url;

	/**
	 * @var string
	 */
	private static string $dir;

	/**
	 * @var string
	 */
	private static string $plugin_base;

	/**
	 * @var string
	 */
	private static string $slug;

	/**
	 * @var string
	 */
	private static string $text_domain;

	/**
	 * @var string
	 */
	private static string $assets_prefix;

	/**
	 * @var string
	 */
	private static string $assets_url;

	/**
	 * @var string
	 */
	private static string $template_dir;

	/**
	 * @var string
	 */
	private static string $log_dir;

	public function __construct( $root ) {
		self::$url              = plugin_dir_url( $root );
		self::$dir              = plugin_dir_path( $root );
		self::$plugin_base      = plugin_basename( $root );
		self::$template_dir     = self::$dir . 'templates/';
		self::$log_dir		    = self::$dir . 'logs/';
		self::$slug             = trim( dirname( self::$plugin_base ), '/' );
		self::$assets_url       = self::$url . 'assets/';
		self::$text_domain      = self::$slug;
		self::$assets_prefix    = 'g28_vistasoft_monitor';
	}

	public static function getInstance( $root ): ?Plugin {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $root );
		}

		return self::$_instance;
	}

	/**
	 * @return string
	 */
	public static function getUrl(): string {
		return self::$url;
	}

	/**
	 * @return string
	 */
	public static function getDir(): string {
		return self::$dir;
	}

	/**
	 * @return string
	 */
	public static function getPluginBase(): string {
		return self::$plugin_base;
	}

	/**
	 * @return string
	 */
	public static function getTemplateDir(): string {
		return self::$template_dir;
	}

	/**
	 * @return string
	 */
	public static function getLogDir(): string {
		return self::$log_dir;
	}

	/**
	 * @return string
	 */
	public static function getSlug(): string {
		return self::$slug;
	}

	/**
	 * @return string
	 */
	public static function getTextDomain(): string {
		return self::$text_domain;
	}

	/**
	 * @return string
	 */
	public static function getAssetsPrefix(): string {
		return self::$assets_prefix;
	}

	/**
	 * @return string
	 */
	public static function getAssetsUrl(): string {
		return self::$assets_url;
	}
}