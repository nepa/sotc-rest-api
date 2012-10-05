<?php

require_once(dirname(__FILE__) . '/BaseController.php');
require_once(dirname(dirname(__FILE__)) . '/Utility.php');
require_once(BACKEND_LOCATION . '/MediaServer.php');
require_once(BACKEND_LOCATION . '/Authentication.php');

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
    $result = array();

    // Report noise level
    if ($request->getSubRessourcePath() == 'report')
    {
      $result = $this->reportNoiseLevel($request);
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
   * Validate the client's API key, store reported noise level
   * value in the database and return a success message.
   *
   * \param $request REST request from client
   *
   * \return Array with response data
   */
  private function reportNoiseLevel($request)
  {
    $result = array();
    $bodyData = $request->getBodyData();
    $arguments = $request->getURLArguments();

    // Validate client credentials
    if (isset($bodyData['AppName']) && isset($bodyData['ApiKey']) &&
        Authentication::validate($bodyData['AppName'], $bodyData['ApiKey']))
    {
      // Report noise level
      if (isset($arguments['latitude']) && isset($arguments['longitude']) && isset($bodyData['Time']) &&
          isset($arguments['zipCode']) && isset($bodyData['NoiseLevel']) && isset($bodyData['NoiseLevelOrg']) &&
          isset($bodyData['ReportedBy']) && isset($bodyData['InPocket']))
      {
        $result = MediaServer::handleReportRequest(
                    $arguments['latitude'],
                    $arguments['longitude'],
                    $bodyData['Time'],
                    $arguments['zipCode'],
                    $bodyData['NoiseLevel'],
                    $bodyData['NoiseLevelOrg'],
                    $bodyData['ReportedBy'],
                    $bodyData['InPocket']);
      }
      else
      {
        $result = array(
                    'Statuscode' => 'Error',
                    'Message' => 'Invalid or incomplete request. Check URL arguments and body data.');
      }
    }
    else
    {
      $result = array(
                  'Statuscode' => 'Error',
                  'Message' => 'Invalid or no auth data provided. Check your API key.');
    }

    return $result;
  }
}

?>
