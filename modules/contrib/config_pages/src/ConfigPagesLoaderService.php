<?php

namespace Drupal\config_pages;

use Drupal\config_pages\Entity\ConfigPages;

/**
 * Class ConfigPagesLoaderService.
 *
 * @package Drupal\config_pages
 */
class ConfigPagesLoaderService implements ConfigPagesLoaderServiceInterface {

  /**
   * Constructor.
   */
  public function __construct() {
  }

  /**
   * Loads config page entity by type and context.
   *
   * @param string $type
   *   Config page type to load.
   * @param string $context
   *   Context which should be used to load entity.
   *
   * @return null|\Drupal\config_pages\Entity\ConfigPages
   *   Loaded CP object.
   */
  public function load($type, $context = NULL) {
    $config_page = ConfigPages::config($type, $context);
    return $config_page;
  }

  /**
   * Get value from CP.
   *
   * @param string|ConfigPages $type
   *   Config page object or type name.
   * @param string $field_name
   *   Field name.
   * @param array|int $deltas
   *   Field value deltas that you like to get.
   * @param string $key
   *   Field "value" key.
   *
   * @return array|mixed|null
   *   Value (or array of values) from specified field in CP.
   */
  public function getValue($type, $field_name, $deltas = [], $key = NULL) {
    $default = !empty($key) ? NULL : [];
    if (!is_array($deltas)) {
      $return_delta = $deltas;
      $deltas = [$deltas];
    }
    else {
      $return_delta = NULL;
    }

    // Exit if empty config page.
    $config_page = is_object($type) ? $type : $this->load($type);
    if (empty($config_page)) {
      return ($return_delta === NULL) ? [] : $default;
    }

    // Load field.
    $field = $config_page->get($field_name);
    if (empty($field)) {
      return ($return_delta === NULL) ? [] : $default;
    }

    // Trim values by deltas.
    $_values = $field->getValue();
    $values = [];
    if (empty($deltas)) {
      $values = $_values;
    }
    else {
      foreach ($deltas as $delta) {
        $values[$delta] = isset($_values[$delta]) ? $_values[$delta] : [];
      }
    }

    // Extract keys from values.
    if (!empty($key)) {
      foreach ($values as &$value) {
        $value = isset($value[$key]) ? $value[$key] : NULL;
      }
    }

    return ($return_delta === NULL) ? $values : $values[$return_delta];
  }

}
