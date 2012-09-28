<?php

/**
 * This is a utility class that provides some useful static
 * functions that are used throughout the whole project.
 *
 * \author seidel
 */
class Utility
{
  /**
   * Helper method to capitalize the first letter of all keys
   * in an associative array. The function will also change
   * the keys of nested arrays.
   *
   * \param &$data Reference to array for conversion
   */
  public static function ucfirstKeys(&$data)
  {
    foreach ($data as $key => $value)
    {
      // Convert key
      $newKey = ucfirst($key);

      // Change key if needed
      if ($newKey != $key)
      {
        unset($data[$key]);
        $data[$newKey] = $value;
      }

      // Handle nested arrays
      if (is_array($value))
      {
        Utility::ucfirstKeys($data[$key]);
      }
    }
  }
}

?>
