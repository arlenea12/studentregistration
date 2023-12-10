<?php

namespace helpers;

class Request {

    public static function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public static function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    public static function getParam($key) {
        if (isset($_POST[$key])) {
            return filter_var($_POST[$key], FILTER_SANITIZE_STRING);
        } else if (isset($_GET[$key])) {
            return filter_var($_GET[$key], FILTER_SANITIZE_STRING);
        } else {
            return null;
        }
    }
}
