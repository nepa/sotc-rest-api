<?php

// Register callback for class autoloading
spl_autoload_register('autoloadClasses');

/**
 * Callback function for class autoloading. The method
 * will automatically require a class with the given
 * name, if it can be found.
 *
 * \param $className Name of the desired class
 */
function autoloadClasses($className)
{
  // Class is a resource controller
  if (preg_match('/^[A-Za-z]+Controller$/', $className))
  {
    $controllerFile = dirname(__FILE__) . '/controller/' . $className . '.php';
    if (file_exists($controllerFile))
    {
      require_once($controllerFile);
    }
    else
    {
      throw new Exception('Could not load \'' . $className . '\' class. There is no such controller.');
    }
  }

  // Class is an output handler
  else if (preg_match('/^[A-Za-z]+OutputHandler$/', $className))
  {
    $outputHandlerFile = dirname(__FILE__) . '/outputHandler/' . $className . '.php';
    if (file_exists($outputHandlerFile))
    {
      require_once($outputHandlerFile);
    }
    else
    {
      throw new Exception('Could not load \'' . $className . '\' class. There is no such output handler.');
    }
  }
}

/**
 * This is the request dispatcher, which will decide how to
 * handle a REST request. It can load the required resource
 * controller and calls the desired action on it (either
 * GET, POST, PUT or DELETE). Afterwards, the result is
 * returned to the client, using one of the output handlers.
 *
 * \author seidel
 */
class Dispatcher
{
  /**
   * Static method to handle REST requests. This includes loading
   * the request controller, calling the right action for the HTTP
   * verb, creating a response and rendering it with the appropriate
   * output handler.
   *
   * \param $request REST request from client
   */
  public static function handleRequest($request)
  {
    // Load controller for requested resource
    $controllerName = ucfirst($request->getRessourcePath()) . 'Controller';

    if (class_exists($controllerName))
    {
      $controller = new $controllerName();

      // Get requested action within controller
      $actionName = strtolower($request->getHTTPVerb()) . 'Action';

      if (method_exists($controller, $actionName))
      {
        // Do the action!
        $result = $controller->$actionName($request);

        // Send REST response to client
        $outputHandlerName = ucfirst($request->getFormat()) . 'OutputHandler';

        if (class_exists($outputHandlerName))
        {
          $outputHandler = new $outputHandlerName();
          $outputHandler->render($result);
        }
      }
    }
  }
}

?>
