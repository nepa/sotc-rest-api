<?php

require_once(dirname(__FILE__) . '/BaseController.php');
require_once('../../s1/MediaServer.php'); // TODO: Remove?
require_once('../../s1/Authentication.php'); // TODO: Remove?

/**
 * This is the device infos controller, which can handle all
 * REST requests related to device infos. The controller will
 * take any request, process it using the corresponding model
 * and prepare a response, that is later sent to the client.
 *
 * \author seidel
 */
class DeviceInfosController extends BaseController
{
  /**
   * This method can handle POST requests to the device info resource.
   * It will store all device information, that has been reported by
   * users, in the database.
   *
   * \param $request REST request from client
   *
   * \return Array with response data
   */
  public function postAction($request)
  {
    $result = array();

    // Report device information
    if ($request->getSubRessourcePath() == 'report')
    {
      $result = $this->reportDeviceInformation($request);
    }
    else
    {
      $result = array(
                  'Statuscode' => 'Error',
                  'Message' => 'Invalid sub-ressource requested for device information.');
    }

    return $result;
  }

  /**
   * Validate the client's API key, store reported device information
   * in the database and return a success message.
   *
   * \param $request REST request from client
   *
   * \return Array with response data
   */
  private function reportDeviceInformation($request)
  {
    $result = array();
    $bodyData = $request->getBodyData();
    $arguments = $request->getURLArguments();

    // Validate client credentials
    if (isset($bodyData['AppName']) && isset($bodyData['ApiKey']) &&
        Authentication::validate($bodyData['AppName'], $bodyData['ApiKey']))
    {
      // Report device information
      if (isset($bodyData['OSVersion']) && isset($bodyData['APILevel']) &&
          isset($bodyData['DeviceType']) && isset($bodyData['ReportedBy']))
      {
        $result = MediaServer::handleReportDeviceInfoRequest(
                    $bodyData['OSVersion'],
                    $bodyData['APILevel'],
                    $bodyData['DeviceType'],
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
