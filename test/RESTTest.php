<?php

require_once(dirname(__FILE__) . '/simpletest/autorun.php');

/**
 * Unit tests for the Sound of the City REST API.
 *
 * \author seidel
 */
class RESTTest extends UnitTestCase
{
  /** Base URL for service endpoint */
  private static $service_url = 'http://localhost/sotc-rest-api/src/';

  /**
   * Test querying of average noise level by geo coordinate.
   */
  public function testAverageNoiseLevel()
  {
    $lat = 51.58;
    $long = 7.6;
    $range = 10.0;
    $query = 'noiseLevels/average/?format=json&latitude=' . $lat . '&longitude=' . $long . '&range=' . $range;

    $response = self::doGETRequest($query);
    $this->assertTrue(!empty($response), 'REST response must not be empty.');

    $this->assertEqual($response['Statuscode'], 'OK', 'Status code must be \'OK\'.');
    $this->assertTrue(is_numeric($response['AverageNoiseLevel']), 'Average noise level must be numeric.');
    $this->assertTrue($response['AverageNoiseLevel'] >= 0, 'Average noise level must be 0 or greater.');
    $this->assertTrue($response['AverageNoiseLevel'] <= 200, 'Average noise level must be 200 or less.');
  }

  /**
   * Test querying of average noise level by zip code.
   */
  public function testAverageNoiseLevelByZipCode()
  {
    $zip = '23562';
    $query = 'noiseLevels/average/?format=json&zipCode=' . $zip;

    $response = self::doGETRequest($query);
    $this->assertTrue(!empty($response), 'REST response must not be empty.');

    $this->assertEqual($response['Statuscode'], 'OK', 'Status code must be \'OK\'.');
    $this->assertTrue(is_numeric($response['AverageNoiseLevel']), 'Average noise level must be numeric.');
    $this->assertTrue($response['AverageNoiseLevel'] >= 0, 'Average noise level must be 0 or greater.');
    $this->assertTrue($response['AverageNoiseLevel'] <= 200, 'Average noise level must be 200 or less.');
  }

  /**
   * Test querying of noise levels by latitude/longitude.
   */
  public function testNoiseLevels()
  {
    $lat = 51.58;
    $long = 7.6;
    $range = 10.0;
    $query = 'noiseLevels/list/?latitude=' . $lat . '&longitude=' . $long . '&range=' . $range;

    $response = self::doGETRequest($query);
    $this->assertTrue(!empty($response), 'REST response must not be empty.');

    $this->assertEqual($response['Statuscode'], 'OK', 'Status code must be \'OK\'.');
    $this->assertTrue(is_array($response['NoiseLevels']), 'Noise levels must be returned as a collection.');
    $this->assertEqual($response['ResultCount'], count($response['NoiseLevels']), 'Number of noise levels must match expected result count.');
  }

  /**
   * Test reporting of noise levels.
   */
  public function testReportNoiseLevels()
  {
    $lat = 51.58;
    $long = 7.6;
    $time = time();
    $zip = '23562';
    $noiseLevel = 70;
    $noiseLevelOrg = 70;
    $reportedBy = 'Foobar';
    $inPocket = 0;
    $appName = 'SotC Android App';
    $apiKey = '62adf8ee76d4b497dd4df5de69ca9f83';
    $query = 'noiseLevels/report/?latitude=' . $lat . '&longitude=' . $long . '&zipCode=' . $zip;

    $body = <<<EOT
{
  "Time": "$time",
  "NoiseLevel": $noiseLevel,
  "NoiseLevelOrg": $noiseLevelOrg,
  "ReportedBy": "$reportedBy",
  "InPocket": $inPocket,
  "AppName": "$appName",
  "ApiKey": "$apiKey"
}
EOT;

    $response = self::doPOSTRequest($query, $body);
    $this->assertTrue(!empty($response), 'REST response must not be empty.');

    $this->assertEqual($response['Statuscode'], 'OK', 'Status code must be \'OK\'.');
  }

  /**
   * Test querying of sound samples by latitude/longitude.
   */
  public function testSamples()
  {
    $lat = 51.58;
    $long = 7.6;
    $range = 10.0;
    $query = 'soundSamples/list/?latitude=' . $lat . '&longitude=' . $long . '&range=' . $range;

    $response = self::doGETRequest($query);
    $this->assertTrue(!empty($response), 'REST response must not be empty.');

    $this->assertEqual($response['Statuscode'], 'OK', 'Status code must be \'OK\'.');
    $this->assertTrue(is_array($response['SampleData']), 'Sound samples must be returned as a collection.');
    $this->assertEqual($response['ResultCount'], count($response['SampleData']), 'Number of sound samples must match expected result count.');
  }

  /**
   * Test uploading of sound samples.
   */
  public function testSampleUpload()
  {
    $lat = 51.58;
    $long = 7.6;
    $title = 'Some title.';
    $timestamp = time();
    $desc = 'Some description.';
    $payloadType = 'mp3';
    $payload = 'xxx';
    $reportedBy = 'Foobar';
    $appName = 'SotC Android App';
    $apiKey = '62adf8ee76d4b497dd4df5de69ca9f83';
    $query = 'soundSamples/upload/?latitude=' . $lat . '&longitude=' . $long;

    $body = <<<EOT
{
  "Title": "$title",
  "Time": "$timestamp",
  "Description": "$desc",
  "PayloadType": "$payloadType",
  "Payload": "$payload",
  "ReportedBy": "$reportedBy",
  "AppName": "$appName",
  "ApiKey": "$apiKey"
}
EOT;

    $response = self::doPOSTRequest($query, $body);
    $this->assertTrue(!empty($response), 'REST response must not be empty.');

    $this->assertEqual($response['Statuscode'], 'OK', 'Status code must be \'OK\'.');
  }

  /**
   * Test reporting of device information.
   */
  public function testReportDeviceInfo()
  {
    $osVersion = '1.0.0';
    $apiLevel = '3';
    $deviceType = 'Some device';
    $reportedBy = 'Foobar';
    $appName = 'SotC Android App';
    $apiKey = '62adf8ee76d4b497dd4df5de69ca9f83';
    $query = 'deviceInfos/report/';

    $body = <<<EOT
{
  "OSVersion": "$osVersion",
  "APILevel": "$apiLevel",
  "DeviceType": "$deviceType",
  "ReportedBy": "$reportedBy",
  "AppName": "$appName",
  "ApiKey": "$apiKey"
}
EOT;

    $response = self::doPOSTRequest($query, $body);
    $this->assertTrue(!empty($response), 'REST response must not be empty.');

    $this->assertEqual($response['Statuscode'], 'OK', 'Status code must be \'OK\'.');
  }

  /**
   * Private helper method to send GET requests via REST.
   *
   * \param $resource Resource query string for REST request
   * \param $decodeJSON Return response as JSON (true) or as
   * decoded associative array (false, default)
   * \param $debug Print REST response (true) or make silent
   * call (false, default)
   *
   * \return REST response, either as JSON code or as array
   */
  private static function doGETRequest($resource, $asJSON = false, $debug = false)
  {
    // Build custom HTTP header
    $options = array(
      'http' => array(
        'method' => 'GET',
        'header' => 'Content-Type: application/json' . "\r\n"
      )
    );
    $context = stream_context_create($options);

    // Request resource from remote site
    $result = @file_get_contents(self::$service_url . $resource, false, $context);
    if ($result === false)
    {
      echo "<p><b>Error:</b> Could not send GET request. Remote site unavailable.</p>";
    }

    // Make debug output
    if ($debug)
    {
      echo '<h3>Request: <i>GET ' . self::$service_url . $resource . '</i></h3>';
      echo '<pre>';
      print_r(json_decode($result, true));
      echo '</pre>';
    }

    // Return result as JSON code or as decoded array
    return ($asJSON ? $result : json_decode($result, true));
  }

  /**
   * Private helper method to send POST requests via REST.
   *
   * \param $resource Resource query string for REST request
   * \param $body Data to send as body of POST request
   * \param $decodeJSON Return response as JSON (true) or as
   * decoded associative array (false, default)
   * \param $debug Print REST response (true) or make silent
   * call (false, default)
   *
   * \return REST response, either as JSON code or as array
   */
  private static function doPOSTRequest($resource, $body, $asJSON = false, $debug = false)
  {
    // Build custom HTTP header
    $options = array(
      'http' => array(
        'method' => 'POST',
        'header' => 'Content-Type: application/json' . "\r\n" .
                    'Content-Length: ' . strlen($body) . "\r\n",
        'content' => $body
      )
    );
    $context = stream_context_create($options);

    // Request resource from remote site
    $result = @file_get_contents(self::$service_url . $resource, false, $context);
    if ($result === false)
    {
      echo "<p><b>Error:</b> Could not send POST request. Remote site unavailable.</p>";
    }

    // Make debug output
    if ($debug)
    {
      echo '<h3>Request: <i>POST ' . self::$service_url . $resource . '</i></h3>';
      echo '<pre>' . $body . '</pre>';
      echo '<pre>';
      print_r(json_decode($result, true));
      echo '</pre>';
    }

    // Return result as JSON code or as decoded array
    return ($asJSON ? $result : json_decode($result, true));
  }
}

?>
