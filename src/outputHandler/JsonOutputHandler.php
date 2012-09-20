<?php

require_once(dirname(__FILE__) . '/BaseOutputHandler.php');

/**
 * This is the output handler for JSON. It can render a REST
 * response in the JSON format. The handler will take an
 * associative array and transform its content to JSON code.
 *
 * \author seidel
 */
class JsonOutputHandler extends BaseOutputHandler
{
  /**
   * This method can render an associative array as JSON code.
   * Furthermore, it will output a HTTP header with the desired
   * format, so that the response is sent to the REST client.
   *
   * \param $data Associative array with response data
   */
  public function render($data)
  {
    // Send HTTP header and JSON payload
    header('Content-Type: application/json; charset=utf8');

    echo json_encode($data, JSON_FORCE_OBJECT);
  }
}

?>
