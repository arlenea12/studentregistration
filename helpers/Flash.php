<?php

namespace helpers;

class Flash {
    public static function setSuccessMessage($message) {
        $_SESSION['flash']['success'] = $message;
    }

    public static function setErrorMessage($message) {
        $_SESSION['flash']['error'] = $message;
    }

    public static function hasSuccessMessage() : bool {
        return isset($_SESSION['flash']['success']) && !empty($_SESSION['flash']['success']);
    }

    public static function hasErrorMessage() : bool {
        return isset($_SESSION['flash']['error']) && !empty($_SESSION['flash']['error']);
    }

    public static function getSuccessMessage() {
        $message = $_SESSION['flash']['success'] ?? null;
        unset($_SESSION['flash']['success']);
        return $message;
    }

    public static function getErrorMessage() {
        $message = $_SESSION['flash']['error'] ?? null;
        unset($_SESSION['flash']['error']);
        return $message;
    }

    public static function has($key) {
        return isset($_SESSION['flash'][$key]) && !empty($_SESSION['flash'][$key]);
    }

    public static function get($key) {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }

    public static function set($key, $message) {
        $_SESSION['flash'][$key] = $message;
    }

}