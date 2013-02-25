<?php

require_once(dirname(__FILE__) . '/BaseController.php');
require_once(dirname(dirname(__FILE__)) . '/Utility.php');
require_once(BACKEND_LOCATION . '/MediaServer.php');

/**
 * This is the use statistics controller, which can handle all
 * REST requests related to logging data. The controller will
 * take any request, process it using the corresponding model
 * and prepare a response, that is later sent to the client.
 *
 * \author seidel
 */
class UseStatsController extends BaseController
{
  /**
   * This method can handle GET requests to the use statistics
   * resource. It will respond with accumulated data about the
   * service usage.
   *
   * \param $request GET request from client
   *
   * \return Array with response data
   */
  public function getAction($request)
  {
    $result = array();

    // Return number of reports
    if ($request->getSubRessourcePath() == 'count')
    {
      $result = $this->getNumberOfReports($request);
    }
    // Return summary of use statistics
    else if ($request->getSubRessourcePath() == 'summary')
    {
      $result = $this->getStatisticsSummary($request);
    }
    else
    {
      $result = array(
                  'Statuscode' => 'Error',
                  'Message' => 'Invalid sub-ressource requested for use statistics.');
    }

    return $result;
  }

  /**
   * Evaluate REST request and return statistical data about
   * the service use (e.g. number of noise level reports).
   *
   * \param $request REST request from client
   *
   * \return Array with response data
   */
  private function getNumberOfReports($request)
  {
    $result = array();
    $arguments = $request->getURLArguments();

    // Time period defaults to 'ever'
    if (!isset($arguments['when']))
    {
      $arguments['when'] = 'ever';
    }

    // Accumulate use data
    if (isset($arguments['what']) && isset($arguments['when']))
    {
      // Prevent PHP notice because of undefined index
      if (!isset($arguments['from']))
      {
        $arguments['from'] = null;
      }
      if (!isset($arguments['to']))
      {
        $arguments['to'] = null;
      }

      // No temporal filtering for app downloads
      if ($arguments['what'] == 'appDownloads')
      {
        $arguments['when'] = 'ever';
      }

      $result = MediaServer::handleStatisticsRequest(
                  $arguments['what'],
                  $arguments['when'],
                  $arguments['from'],
                  $arguments['to']);

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
   * Evaluate REST request and return a summary of the service's
   * use statistics for desired content type (e.g. noise level
   * reports or sound sample uploads).
   *
   * \param $request REST request from client
   *
   * \return Array with response data
   */
  private function getStatisticsSummary($request)
  {
    $result = array();
    $arguments = $request->getURLArguments();

    // Summarize use statistics
    if (isset($arguments['what']))
    {
      $result = MediaServer::handleStatisticsSummaryRequest($arguments['what']);

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
}

?>
