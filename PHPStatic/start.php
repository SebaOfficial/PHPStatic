<?php

namespace PHPStatic;

require_once __DIR__ . "/envirorment.php";

exec("composer build-on-changes  > /dev/null 2>&1 &");

$sys::message("Starting Web Server at http://$server->address:$server->port\n", 1);

$server->start($paths->dist);