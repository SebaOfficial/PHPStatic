<?php

require_once __DIR__ . "/envirorment.php";

function addWatchRecursively($inotify, $directory) {
    $watchDescriptor = inotify_add_watch($inotify, $directory, IN_MODIFY | IN_CREATE | IN_DELETE | IN_ISDIR);

    $subdirs = glob("$directory/*", GLOB_ONLYDIR);

    foreach ($subdirs as $subdir) {
        addWatchRecursively($inotify, $subdir);
    }
}

$inotify = inotify_init();

addWatchRecursively($inotify, ROOT_DIR . "/src");

while (true) {
    $events = inotify_read($inotify);

    if ($events !== false) {

        exec("composer build > /dev/null 2>&1 ", $output, $statusCode);

        if($statusCode !== 0) {
            $build::message("Failed to update.\n", 3);
            exit(1);
        }
    }
}

inotify_rm_watch($inotify, $watchDescriptor);
fclose($inotify);
