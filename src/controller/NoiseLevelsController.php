<?php

require_once(dirname(__FILE__) . '/BaseController.php');
require_once('../../s1/MediaServer.php'); // TODO: Remove?

/**
 * This is the noise levels controller, which can handle all
 * REST requests related to noise levels. The controller will
 * take any request, process it using the corresponding model
 * and prepare a response, that is later sent to the client.
 *
 * \author seidel
 */
class NoiseLevelsController extends BaseController
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
    $result = array();

    // Return average noise level
    if ($request->getSubRessourcePath() == 'average')
    {
      $result = $this->getAverageNoiseLevel($request);
    }
    // Return list of noise levels
    else if ($request->getSubRessourcePath() == 'list')
    {
      $result = $this->getNoiseLevels($request);
    }
    else
    {
      $result = array(
                  'Statuscode' => 'Error',
                  'Message' => 'Invalid sub-ressource requested for noise levels.');
    }

    return $result;
  }

  /**
   * Evaluate REST request and return average noise level,
   * either by geo location or by zip code.
   *
   * \param $request REST request from client
   *
   * \return Array with response data
   */
  private function getAverageNoiseLevel($request)
  {
    $result = array();
    $arguments = $request->getURLArguments();

    // Average noise level by geo location
    if (isset($arguments['latitude']) && isset($arguments['longitude']) && isset($arguments['range']))
    {
      $result = MediaServer::handleAverageNoiseLevelRequest(
                  $arguments['latitude'],
                  $arguments['longitude'],
                  $arguments['range']);
    }
    // Average noise level by zip code
    else if (isset($arguments['zipCode']))
    {
      $result = MediaServer::handleAverageNoiseLevelByZipCodeRequest(
                  $arguments['zipCode']);
    }
    else
    {
      $result = array(
                  'Statuscode' => 'Error',
                  'Message' => 'Invalid or no arguments in REST request.');
    }

    return $result;
  }

  /**
   * Evaluate REST request and return list of noise levels
   * by geo location.
   *
   * \param $request REST request from client
   *
   * \return Array with response data
   */
  private function getNoiseLevels($request)
  {
    $result = array();
    $arguments = $request->getURLArguments();

    // List noise levels by geo location
    if (isset($arguments['latitude']) && isset($arguments['longitude']) && isset($arguments['range']))
    {
      $result = MediaServer::handleNoiseLevelsRequest(
                  $arguments['latitude'],
                  $arguments['longitude'],
                  $arguments['range']);

      // Capitalize first letter of all array keys
      $this->ucfirstKeys($result);
    }

    return $result;
  }

  /**
   * Private helper method to capitalize the first letter
   * of all array keys. The function will also change the
   * keys of all nested arrays.
   *
   * \param &$data Reference to array for conversion
   */
  private function ucfirstKeys(&$data)
  {
    foreach ($data as $key => $value)
    {
      // Convert key
      $newKey = ucfirst($key);

      // Change key if needed
      if ($newKey != $key)
      {
        unset($data[$key]);
        $data[$newKey] = $value;
      }

      // Handle nested arrays
      if (is_array($value))
      {
        $this->ucfirstKeys($data[$key]);
      }
    }
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
