<?php

require __DIR__ . "/envirorment.php";

$id = uniqid();

$sys::rcopy(ROOT_DIR . "/docs/", $paths->pages . $id . '/');

exec("composer build");

$sys::rcopy(ROOT_DIR . "/docs/public/", $paths->dist);

$sys::rrmdir($paths->pages . $id . '/', true);


$sys::message("\nDocs can be found at http://$server->address:$server->port/$id/\n\n", 2);

$server->start($paths->dist);