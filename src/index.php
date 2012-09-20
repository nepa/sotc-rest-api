<?php

/**
 * API for REST request handling.
 */

// TODO: error_reporting(0); // No error reporting in live environment

require_once(dirname(__FILE__) . '/Request.php');
require_once(dirname(__FILE__) . '/Dispatcher.php');

// Populate request object
$request = null;
try
{
  $request = new Request();
}
catch (Exception $e)
{
  echo '<p><b>Error:</b> ' . $e->getMessage() . '</p>';
}

// Handle request
if (!is_null($request))
{
  Dispatcher::handleRequest($request);
}

?>
