<?php

namespace helpers;

class Input {

  private static $data = [];

  public static function init($data) {
    self::$data = $data;
  }

  public static function hasPrev($fieldName) {
    if (isset(self::$data[$fieldName]) && !empty(self::$data[$fieldName])) {
      return true;
    } elseif (isset($_POST[$fieldName]) && !empty($_POST[$fieldName])) {
      return true;
    }
    return false;
  }

  public static function getPrev($fieldName) {
    if (isset(self::$data[$fieldName])) {
      return self::$data[$fieldName];
    } elseif (isset($_POST[$fieldName])) {
      return $_POST[$fieldName];
    }
    return '';
  }

  public static function isSelected($fieldName, $fieldValue) {
    if (isset(self::$data[$fieldName]) && self::$data[$fieldName] == $fieldValue) {
      return 'selected';
    } elseif (isset($_POST[$fieldName]) && $_POST[$fieldName] == $fieldValue) {
      return 'selected';
    }
    return '';
  }

  public static function isChecked($fieldName, $fieldValue) {
    if (isset(self::$data[$fieldName]) && self::$data[$fieldName] == $fieldValue) {
      return 'checked';
    } elseif (isset($_POST[$fieldName]) && $_POST[$fieldName] == $fieldValue) {
      return 'checked';
    }
    return '';
  }

}
