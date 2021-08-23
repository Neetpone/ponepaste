<?php

class ViewBag {
    private static array $global = [];
    private static array $local = [];

    public static function putGlobal(string $key, string $value) : void {
        ViewBag::$global[$key] = $value;
    }

    public static function put(string $key, string $value) : void {
        ViewBag::$local[$key] = $value;
    }

    public static function getGlobal(string $key, bool $escape = true) : string {
        $value = ViewBag::$global[$key];

        if ($escape) {
            $value = pp_html_escape($value);
        }

        return $value;
    }

    public static function get(string $key, bool $escape = true) : string {
        $value = ViewBag::$local[$key];

        if ($escape) {
            $value = pp_html_escape($value);
        }

        return $value;
    }
}