<?php

//use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;

$allowedIPs = array('192.168.1.74', '192.168.1.51');
$sitioOffline = false;

if ($sitioOffline) { 

	if (isset($_SERVER['HTTP_CLIENT_IP'])
		|| isset($_SERVER['HTTP_X_FORWARDED_FOR'])
		|| !in_array(@$_SERVER['REMOTE_ADDR'], $allowedIPs)
	) {
		header('HTTP/1.0 403 Forbidden');
		exit('<h3>El sitio se encuentra en mantenimiento. Disculpe las molestias ocacionadas.</h3>');
	}
}

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

// Use APC for autoloading to improve performance.
// Change 'sf2' to a unique prefix in order to prevent cache key conflicts
// with other applications also using APC.

/*
$apcLoader = new ApcClassLoader('sf2adif', $loader);
$loader->unregister();
$apcLoader->register(true);
*/

require_once __DIR__.'/../app/AppKernel.php';
//require_once __DIR__.'/../app/AppCache.php';

$kernel = new AppKernel('prod', true);
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
