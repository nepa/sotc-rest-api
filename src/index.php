<?php

/**
 * API for REST request handling.
 */

// TODO: Deactivate error reporting
// On live server: 0
error_reporting(0); // No error reporting in live environment

// TODO: Set path to Server-One
// On live server: ../../s1
// Location of service backend
define('BACKEND_LOCATION', '../../s1'); // Relative to API path; NO trailing slash!

require_once(dirname(__FILE__) . '/Request.php');
require_once(dirname(__FILE__) . '/Dispatcher.php');

// Populate request object
$request = null;
try
{
  $request = new Request();

  // Handle request
  if (!is_null($request))
  {
    Dispatcher::handleRequest($request);
  }
}
catch (Exception $e)
{
  echo 'Error: ' . $e->getMessage();
}

?>
