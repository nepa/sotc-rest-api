<?php

require_once(dirname(__FILE__) . '/BaseController.php');
require_once('../../s1/MediaServer.php'); // TODO: Remove?

/**
 * This is the noise level controller, which can handle all
 * REST requests related to noise levels. The controller will
 * take any request, process it using the corresponding model
 * and prepare a response, that is later sent to the client.
 *
 * \author seidel
 */
class NoiseLevelController extends BaseController
{
  /**
   * This method can handle GET requests to the noise level resource.
   * It will either return the average noise level of a geo location
   * or a list of specific noise levels in a desired area.
   *
   * \param $request REST request from client
   *
   * \return Array with response data
   */
  public function getAction($request)
  {
    echo 'GET action of noise level controller was called.' . "\n\n"; // TODO: Remove

    $arguments = $request->getURLArguments();
    $result = MediaServer::handleAverageNoiseLevelRequest(
                $arguments['latitude'],
                $arguments['longitude'],
                $arguments['range']);

    return $result;
  }

  /**
   * This method can handle POST requests to the noise level resource.
   * It will store single noise level values, that have been reported
   * by users.
   *
   * \param $request REST request from client
   *
   * \return Array with response data
   */
  public function postAction($request)
  {
    echo 'POST action of noise level controller was called.' . "\n\n";

    // TODO: Implement method
  }
}

?>
