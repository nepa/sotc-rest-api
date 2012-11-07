<?php

/**
 * This is the abstract base class of all output handlers.
 *
 * \author seidel
 */
abstract class BaseOutputHandler
{
  /**
   * This method can render an associative array in the
   * desired output format, based on the implementation
   * of the concrete output handler (e.g. JSON or XML).
   *
   * \param $data Associative array with response data
   */
  abstract public function render($data);

  /**
   * Helper method to encode all values of an associative
   * array with UTF-8. The function will also change the
   * values of nested arrays.
   *
   * \param &$data Reference to array for conversion
   */
  protected function utf8Values(&$data)
  {
    foreach ($data as $key => $value)
    {
      // Convert atomic values to UTF-8
      if (!is_array($value))
      {
        $data[$key] = utf8_encode($value);
      }

      // Handle nested arrays
      if (is_array($value))
      {
        $this->utf8Values($data[$key]);
      }
    }
  }
}

?>
