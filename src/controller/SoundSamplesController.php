<?php

require_once(dirname(__FILE__) . '/BaseController.php');
require_once(dirname(dirname(__FILE__)) . '/Utility.php');
require_once(BACKEND_LOCATION . '/MediaServer.php');
require_once(BACKEND_LOCATION . '/Authentication.php');

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

  /**
   * This method can handle POST requests to the sound sample resource.
   * It will save single sound samples, that have been uploaded by the
   * user.
   *
   * \param $request REST request from client
   *
   * \return Array with response data
   */
  public function postAction($request)
  {
    $result = array();

    // Upload sound sample
    if ($request->getSubRessourcePath() == 'upload')
    {
      $result = $this->uploadSoundSample($request);
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
   * Validate the client's API key, save uploaded sound sample on
   * disk and return a success message.
   *
   * \param $request REST request from client
   *
   * \return Array with response data
   */
  private function uploadSoundSample($request)
  {
    $result = array();
    $bodyData = $request->getBodyData();
    $arguments = $request->getURLArguments();

    // Validate client credentials
    if (isset($bodyData['AppName']) && isset($bodyData['ApiKey']) &&
        Authentication::validate($bodyData['AppName'], $bodyData['ApiKey']))
    {
      // Upload sound sample
      if (isset($arguments['latitude']) && isset($arguments['longitude']) && isset($bodyData['Time']) &&
          isset($bodyData['Tag']) && isset($bodyData['PayloadType']) && isset($bodyData['Payload']) &&
          isset($bodyData['ReportedBy']))
      {
        // Prevent PHP notice because of undefined index
        if (!isset($bodyData['Title']))
        {
          $bodyData['Title'] = null;
        }
        if (!isset($bodyData['Description']))
        {
          $bodyData['Description'] = null;
        }

        $result = MediaServer::handleUploadRequest(
                    $arguments['latitude'],
                    $arguments['longitude'],
                    $bodyData['Title'],
                    $bodyData['Time'],
                    $bodyData['Description'],
                    $bodyData['Tag'],
                    $bodyData['PayloadType'],
                    $bodyData['Payload'],
                    $bodyData['ReportedBy']);
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
