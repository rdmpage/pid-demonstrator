<?php


global $config;

// Date timezone--------------------------------------------------------------------------
date_default_timezone_set('UTC');

// Multibyte strings----------------------------------------------------------------------
mb_internal_encoding("UTF-8");

// Hosting--------------------------------------------------------------------------------

$site = 'local';
$site = 'heroku';

switch ($site)
{
	case 'heroku':
		// Server-------------------------------------------------------------------------
		$config['web_server']	= 'https://pid-demonstrator.herokuapp.com'; 
		$config['site_name']	= 'PID Demonstrator';

		// Files--------------------------------------------------------------------------
		$config['web_dir']		= dirname(__FILE__);
		$config['web_root']		= '/';		
		break;

	case 'local':
	default:
		// Server-------------------------------------------------------------------------
		$config['web_server']	= 'http://localhost'; 
		$config['site_name']	= 'PID Demonstrator';

		// Files--------------------------------------------------------------------------
		$config['web_dir']		= dirname(__FILE__);
		$config['web_root']		= '/~rpage/pid-demonstrator/';
		break;
}

// Environment----------------------------------------------------------------------------
// In development this is a PHP file that is in .gitignore, when deployed these parameters
// will be set on the server
if (file_exists(dirname(__FILE__) . '/env.php'))
{
	include 'env.php';
}

$config['cache']					= dirname(__FILE__) . '/cache';



?>
