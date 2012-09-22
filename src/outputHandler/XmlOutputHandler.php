<?php

require_once(dirname(__FILE__) . '/BaseOutputHandler.php');

/**
 * This is the output handler for XML. It can render a REST
 * response as a XML document. The handler will take an
 * associative array and transform its content to XML code.
 *
 * \author seidel
 */
class XmlOutputHandler extends BaseOutputHandler
{
  /**
   * This method can render an associative array as XML code.
   * Furthermore, it will output a HTTP header with the desired
   * format, so that the response is sent to the REST client.
   *
   * \param $data Associative array with response data
   */
  public function render($data)
  {
    // Send HTTP header and XML payload
    header('Content-Type: text/xml; charset=utf8');

    $this->printXML($data);
  }

  /**
   * Print array with response data as XML document.
   *
   * \param $data Associative array with response data
   */
  private function printXML($data)
  {
    // Create SimpleXML object from array
    $simpleXml = $this->convertToSimpleXml($data);

    // Transform to DOM object for pretty printing
    $domXml = dom_import_simplexml($simpleXml)->ownerDocument;
    $domXml->formatOutput = true;

    echo $domXml->saveXML();
  }

  /**
   * Convert an associative array to a SimpleXML object.
   *
   * \param $data Associative array with data
   * \param $simpleXml SimpleXML object for recursive
   * method calls (pass 'null' when calling function
   * for the first time)
   *
   * \return SimpleXML object with array content
   */
  private function convertToSimpleXml($data, $simpleXml = null)
  {
    // Create new SimpleXML object, if none is passed
    if (is_null($simpleXml))
    {
      $simpleXml = new SimpleXMLElement('<Response/>');
    }

    // Iterate all field entries
    foreach ($data as $key => $value)
    {
      // Numeric element names are not valid XML:
      //   <0>value</0> -> <Item>value</Item>
      $key = (is_numeric($key) ? 'Item' : $key);

      // Recursively process nested arrays
      if (is_array($value))
      {
        $this->convertToSimpleXml($value, $simpleXml->addChild($key));
      }
      // Process atomic values
      else
      {
        $simpleXml->addChild($key, $value);
      }
    }

    return $simpleXml;
  }
}
