<?php
/**
 * CodeIgniter - index.php (versi sudah diperbaiki untuk PHP 8.2+)
 */

/*
 *---------------------------------------------------------------
 * Application Environment
 *---------------------------------------------------------------
 */
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

/*
 *---------------------------------------------------------------
 * Error Reporting (SUDAH DIMODIFIKASI: menyembunyikan deprecated PHP 8.2+)
 *---------------------------------------------------------------
 */
switch (ENVIRONMENT)
{
	case 'development':
		error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
		ini_set('display_errors', 1);
	break;

	case 'testing':
	case 'production':
		ini_set('display_errors', 0);
		error_reporting(0);
	break;

	default:
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'The application environment is not set correctly.';
		exit(1);
}

/*
 *---------------------------------------------------------------
 * System Path
 *---------------------------------------------------------------
 */
$system_path = 'system';

/*
 *---------------------------------------------------------------
 * Application Path
 *---------------------------------------------------------------
 */
$application_folder = 'application';

/*
 *---------------------------------------------------------------
 * View Path
 *---------------------------------------------------------------
 */
$view_folder = '';

/*
 * ---------------------------------------------------------------
 * Set the current directory correctly for CLI requests
 * ---------------------------------------------------------------
 */
if (defined('STDIN'))
{
	chdir(dirname(__FILE__));
}

if (($_temp = realpath($system_path)) !== FALSE)
{
	$system_path = $_temp.DIRECTORY_SEPARATOR;
}
else
{
	// Ensure there's a trailing slash
	$system_path = strtr(
		rtrim($system_path, '/\\'),
		'/\\',
		DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
	).DIRECTORY_SEPARATOR;
}

// Is the system path correct?
if ( ! is_dir($system_path))
{
	header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
	echo 'Your system folder path does not appear to be set correctly. Please open the following file and correct this: '.pathinfo(__FILE__, PATHINFO_BASENAME);
	exit(3);
}

/*
 *---------------------------------------------------------------
 * Define constants
 *---------------------------------------------------------------
 */
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('BASEPATH', $system_path);
define('FCPATH', str_replace(SELF, '', __FILE__));

if (is_dir($application_folder))
{
	define('APPPATH', realpath($application_folder).DIRECTORY_SEPARATOR);
}
else
{
	if ( ! is_dir(BASEPATH.$application_folder.DIRECTORY_SEPARATOR))
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your application folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
		exit(3);
	}

	define('APPPATH', BASEPATH.$application_folder.DIRECTORY_SEPARATOR);
}

if (is_dir($view_folder))
{
	define('VIEWPATH', realpath($view_folder).DIRECTORY_SEPARATOR);
}
else
{
	if ( ! is_dir(APPPATH.'views'.DIRECTORY_SEPARATOR))
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your view folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
		exit(3);
	}

	define('VIEWPATH', APPPATH.'views'.DIRECTORY_SEPARATOR);
}

/*
 *---------------------------------------------------------------
 * Load the framework
 *---------------------------------------------------------------
 */
require_once BASEPATH.'core/CodeIgniter.php';