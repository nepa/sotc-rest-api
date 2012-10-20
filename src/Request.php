<?php

/**
 * This class represents REST requests and all their data
 * (e.g. HTTP verb, request format, URL elements and body
 * data of the request).
 *
 * \author seidel
 */
class Request
{
  /** Offset when returning ressource and sub-ressource part of URL path */
  private $pathOffset = 3;

  /** Request method (either GET, POST, PUT or DELETE) */
  private $httpVerb;

  /** Request format (e.g. JSON or form data) */
  private $format;

  /** URL path of requested resource */
  private $urlPath;

  /** Query arguments from URL */
  private $urlArguments;

  /** Additional data from request body */
  private $bodyData;

  /**
   * Create a new RESTRequest object and initialize all its
   * member variables from PHP's global $_SERVER array.
   */
  public function __construct()
  {
    // GET, POST, PUT or DELETE
    $this->httpVerb = $_SERVER['REQUEST_METHOD'];

    // JSON, XML or whatever
    $this->format = 'json';

    // http://api.example.com/users/alice/foo -> users, alice and foo
    $this->urlPath = explode('/', $_SERVER['REQUEST_URI']);

    // Parse URL arguments and parse request body
    try
    {
      $this->parseArguments($this->urlArguments, $this->bodyData);
    }
    catch (Exception $e)
    {
      throw $e;
    }

    // User is requesting format other than JSON via URL
    if (isset($this->urlArguments['format']))
    {
      $this->format = strtolower($this->urlArguments['format']);
    }
  }

  /**
   * Private helper method to extract URL arguments and parse
   * the body of POST/PUT requests.
   *
   * \param &$urlArguments Reference to a field, that will
   * be used to return parsed URL arguments
   * \param &$bodyData Reference to a field, that will be
   * used to return parsed body data
   */
  private function parseArguments(&$urlArguments, &$bodyData)
  {
    // Extract GET arguments from URL
    $arguments = array();

    if (isset($_SERVER['QUERY_STRING']))
    {
      parse_str($_SERVER['QUERY_STRING'], $arguments);
    }
    $urlArguments = $arguments;

    /****************************************************************/

    // Extract body of POST/PUT requests
    $body = array();

    $content_type = '';
    if (isset($_SERVER['CONTENT_TYPE']))
    {
      $content_type = $_SERVER['CONTENT_TYPE'];
    }

    // Parse body depending on content type
    switch ($content_type)
    {
      case 'application/json':
        $this->extractJSON($body);
        break;

      case 'application/x-www-form-urlencoded':
        $this->extractFormData($body);
        break;

      default:
        if (empty($content_type))
        {
          throw new Exception('No content type in request. Please set the \'Content-Type\' header.');
        }
        else
        {
          throw new Exception('Unsupported content type \'' . $content_type . '\' in request.');
        }
        break;
    }
    $bodyData = $body;
  }

  /**
   * Private helper method to extract JSON code from request body.
   * This function will decode the JSON body and copy all values
   * to the field that was passed in by reference.
   *
   * Request format will be set to 'json'.
   *
   * \param &$body Reference to a field, that will be used to
   * store the parsed body arguments in it
   */
  private function extractJSON(&$body)
  {
    $bodyPayload = array();
    $bodyPayload = json_decode(file_get_contents("php://input"));

    if ($bodyPayload)
    {
      foreach ($bodyPayload as $key => $value)
      {
        $body[$key] = $value;
      }
    }

    $this->format = 'json';
  }

  /**
   * Private helper method to extract form data from request body.
   * This function will parse the form arguments and copy their
   * values to the field that was passed in by reference.
   *
   * Request format will be set to 'html';
   *
   * \param &$body Reference to a field, that will be used to
   * store the parsed body arguments in it
   */
  private function extractFormData(&$body)
  {
    $bodyPayload = array();
    parse_str(file_get_contents("php://input"), $bodyPayload);

    if ($bodyPayload)
    {
      foreach ($bodyPayload as $key => $value)
      {
        $body[$key] = $value;
      }
    }

    $this->format = 'html';
  }

  /**
   * Getter for HTTP verb that was used to send the
   * REST request (either GET, POST, PUT or DELETE).
   *
   * \return HTTP verb
   */
  public function getHTTPVerb()
  {
    return $this->httpVerb;
  }

  /**
   * Getter for request format (e.g. JSON or form data).
   *
   * \return Request format
   */
  public function getFormat()
  {
    return $this->format;
  }

  /**
   * Getter for parts of the URL path. The index can
   * be used to select dedicated elements of the URL
   * path (e.g. http://api.example.com/this/is/path).
   *
   * \param $id Index of desired URL part (>= 1)
   *
   * \return Desired part of the URL path
   */
  public function getURLPath($id = 1)
  {
    // Normalize index
    if ($id < 1)
    {
      $id = 1;
    }
    else if ($id > count($this->urlPath) - 1)
    {
      $id = count($this->urlPath) - 1;
    }

    return $this->urlPath[$id];
  }

  /**
   * Get ressource part of the URL path. E.g. for
   * http://api.example.com/customer/list this
   * method will return 'customer'.
   *
   * \return Ressource part of URL path
   */
  public function getRessourcePath()
  {
    return $this->getURLPath($this->pathOffset);
  }

  /**
   * Get sub-ressource part of the URL path. E.g.
   * for http://api.example.com/customer/list this
   * method will return 'list'.
   *
   * \return Sub-ressource part of URL path
   */
  public function getSubRessourcePath()
  {
    return $this->getURLPath($this->pathOffset + 1);
  }

  /**
   * Getter for query arguments from the URL. The method
   * will return an associative array with pairs of keys
   * and values.
   *
   * \return Array with query arguments from URL
   */
  public function getURLArguments()
  {
    return $this->urlArguments;
  }

  /**
   * Getter for aditional data from the request body. The
   * method will return an associative array with the data
   * from the request body.
   *
   * \return Array with additional data from the request body
   */
  public function getBodyData()
  {
    return $this->bodyData;
  }
}

?>
