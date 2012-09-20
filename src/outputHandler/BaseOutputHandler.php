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
}

?>
