<?php

require_once(dirname(__FILE__) . '/BaseController.php');
require_once(dirname(dirname(__FILE__)) . '/Utility.php');
require_once('../../s1/MediaServer.php'); // TODO: Remove?
require_once('../../s1/Authentication.php'); // TODO: Remove?

/**
 * This is the sound sample controller, which can handle all
 * REST requests related to sound samples. The controller will
 * take any request, process it using the corresponding model
 * and prepare a response, that is later sent to the client.
 *
 * \author seidel
 */
class SoundSamplesController extends BaseController
{
  /**
   * This method can handle GET requests to the sound sample
   * resource. It will return a list with metadata for sound
   * samples in a specific area.
   *
   * \param $request REST request from client
   *
   * \return Array with response data
   */
  public function getAction($request)
  {
    $result = array();

    // Return list of sound samples
    if ($request->getSubRessourcePath() == 'list')
    {
      $result = $this->getSoundSamples($request);
    }
    else
    {
      $result = array(
                  'Statuscode' => 'Error',
                  'Message' => 'Invalid sub-ressource requested for sound samples.');
    }

    return $result;
  }

  /**
   * Evaluate REST request and return a list of sound
   * samples by geo location.
   *
   * \param $request REST request from client
   *
   * \return Array with response data
   */
  private function getSoundSamples($request)
  {
    $result = array();
    $arguments = $request->getURLArguments();

    // List sound samples by geo location
    if (isset($arguments['latitude']) && isset($arguments['longitude']) && isset($arguments['range']))
    {
      $result = MediaServer::handleSamplesRequest(
                  $arguments['latitude'],
                  $arguments['longitude'],
                  $arguments['range']);

      // Capitalize first letter of all array keys
      Utility::ucfirstKeys($result);
    }
    else
    {
      $result = array(
                  'Statuscode' => 'Error',
                  'Message' => 'Invalid or no arguments in REST request.');
    }

    return $result;
  }

  // TODO: Implement method and add comment
  public function postAction($request)
  {
  }
}

?>
