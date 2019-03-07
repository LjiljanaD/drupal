<?php

namespace Drupal\hello_world\Controller;

class HelloWorldController {
  public function hello2() {

    $countries = array(
      array('name' => 'Country one'),
      array('name' => 'Country two'),
      array('name' => 'Country three'),
    );

    return array (
      '#title' => 'Hello World!',
      '#theme' => 'my_test_theme_2',
      '#markup' => 'Content for Hello World',
      '#countries' => $countries,
    );
  }
}
