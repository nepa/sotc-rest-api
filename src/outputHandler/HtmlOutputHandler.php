<?php

require_once(dirname(__FILE__) . '/BaseOutputHandler.php');

/**
 * This is the output handler for HTML. It can render a REST
 * response as plain HTML code. The handler will take an
 * associative array and transform its content to a HTML page.
 *
 * The page can easily be viewed in a webbrowser and is thus
 * especially suitable for debug purposes.
 *
 * \author seidel
 */
class HtmlOutputHandler extends BaseOutputHandler
{
  /**
   * This method can render an associative array as HTML code.
   * Furthermore, it will output a HTTP header with the desired
   * format, so that the response is sent to the REST client.
   *
   * \param $data Associative array with response data
   */
  public function render($data)
  {
    // Send HTTP header and HTML payload
    header('Content-Type: text/html; charset=utf8');

    $this->printHTMLHeader();
    $this->printArray($data);
    $this->printHTMLFooter();
  }

  /**
   * Print array with response data as HTML list.
   *
   * \param $data Associative array with response data
   * \param $level Level for list indention when printing
   * nested arrays (default is 0)
   */
  private function printArray($data, $level = 0)
  {
    $spaces = '    ';
    for ($i = 0; $i < $level; $i++)
    {
      $spaces .= '    ';
    }

    echo "\n" . $spaces . '<ul>' . "\n";

    // Iterate all field entries
    foreach ($data as $key => $value)
    {
      echo $spaces . '  <li>' . "\n";
      echo $spaces . '    <b>' . $key . '</b>: ';

      // Recursively process nested arrays
      if (is_array($value))
      {
        $this->printArray($value, $level + 1);
      }
      // Process atomic values
      else
      {
        $value = htmlentities($value, ENT_COMPAT, 'UTF-8');

        // Print URLs as a clickable link
        if (strpos($value, 'http://') === 0 || strpos($value, 'https://') === 0)
        {
          echo '<a href="' . $value . '">' . $value . '</a>';
        }
        // Print normal text
        else
        {
          echo $value;
        }
      }

      echo "\n" . $spaces . '  </li>' . "\n";
    }

    echo $spaces . '</ul>';
  }

  /**
   * Print header of HTML page.
   */
  private function printHTMLHeader()
  {
    echo <<<EOT
<!DOCTYPE html>
<html>
  <head>
    <title>Sound of the City REST API</title>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  </head>

  <body>
EOT;
  }

  /**
   * Print footer of HTML page.
   */
  private function printHTMLFooter()
  {
    echo <<<EOT

  </body>
</html>
EOT;
  }
}
