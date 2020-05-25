<?php
namespace App\services;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class StaticLogger {
	protected static $instance;

	/**
	 * Method to return the Monolog instance
	 *
	 * @return \Monolog\Logger
	 */
	static public function getLogger()
	{
		if (! self::$instance) {
			self::configureInstance();
		}

		return self::$instance;
	}

	/**
	 * Configure Monolog to use a rotating files system.
	 *
	 * @return Logger
	 */
	protected static function configureInstance()
	{
		$dir = SERVER_ROOT.'/log';

		if (!file_exists($dir)){
			mkdir($dir, 0777, true);
		}

		$logger = new Logger('');
		$logger->pushHandler(new RotatingFileHandler($dir.'/main.log', 5));
		//$logger->pushHandler(new LogglyHandler('eeb5ba83-f0d6-4273-bb1d-523231583855/tag/monolog'));
		self::$instance = $logger;
	}

	public static function debug($message, array $context = []){
		self::getLogger()->debug($message, $context);
	}

	public static function info($message, array $context = []){
		self::getLogger()->info($message, $context);
	}

	public static function notice($message, array $context = []){
		self::getLogger()->notice($message, $context);
	}

	public static function warning($message, array $context = []){
		self::getLogger()->warning($message, $context);
	}

	public static function error($message, array $context = []){
		self::getLogger()->error($message, $context);
	}

	public static function critical($message, array $context = []){
		self::getLogger()->critical($message, $context);
	}

	public static function alert($message, array $context = []){
		self::getLogger()->alert($message, $context);
	}

	public static function emergency($message, array $context = []){
		self::getLogger()->emergency($message, $context);
	}

}

    