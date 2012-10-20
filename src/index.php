<?php

/**
 * API for REST request handling.
 */

// TODO: error_reporting(0); // No error reporting in live environment

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
