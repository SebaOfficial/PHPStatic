<?php

namespace PHPStatic;

define("ROOT_DIR", dirname(__DIR__));

require_once ROOT_DIR . "/vendor/autoload.php";

$settings = json_decode(strtr(file_get_contents(ROOT_DIR . "/phpstatic.json"), [
    "@" => ROOT_DIR
]));

$build = new Builder();
$sys = new Sys();
$server = new Server(
    $settings->server->address ?? "localhost",
    $settings->server->port ?? null
);

$paths = (object)[
    "template" => $settings->pages->template ??  (ROOT_DIR . "/src/template.html"),
    "dist" => $settings->pages->distDir ??  (ROOT_DIR . "/dist/"),
    "pages" => $settings->pages->path ??  (ROOT_DIR . "/src/pages/"),
    "languages" => $settings->languages->path ??  (ROOT_DIR . "/src/locales/"),
    "public" => $settings->pages->public_path ??  (ROOT_DIR . "/src/public/"),
];