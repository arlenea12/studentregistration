<?php

namespace helpers;

class Redirect {
    public static function to($url) {
        header("Location: {$url}");
        exit();
    }

    public static function back() {
        header("Location: " . self::get_redirect_back_path());
        exit();
    }

    public static function backOr($url) {
        if (self::has_redirect_back_path()) {
            header("Location: " . self::get_redirect_back_path());
        } else {
            header("Location: {$url}");
        }
        exit();
    }

    public static function set_redirect_back_path($path) {
        $_SESSION['redirect_back_path'] = $path;
    }

    public static function has_redirect_back_path() {
        return isset($_SESSION['redirect_back_path']) && !empty($_SESSION['redirect_back_path']);
    }

    public static function get_redirect_back_path() {
        $path = $_SESSION['redirect_back_path'] ?? null;
        unset($_SESSION['redirect_back_path']);
        return $path;
    }

    public static function reset_redirect_back_path() {
        if (isset($_SESSION['redirect_back_path'])) {
            unset($_SESSION['redirect_back_path']);
        }
    }
}
