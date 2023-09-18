<?php

namespace PHPStatic;

require_once __DIR__ . "/envirorment.php";

$arguments = getopt("", ["seo"]);
$availableExtensions = ["html", "php", "md"];

$sys::message("Starting Build process...\n", 1);


$sys::message("Loading template...", 1);

if(!file_exists($paths->template)) {
    $sys::message("Template could't be loaded: $paths->template doesn't exist.\n", 3, true);
    exit(1);
}

$template = file_get_contents($paths->template);

$sys::message("✔ Template loaded.\n", 2, true);


if(is_dir($paths->dist)){
    $sys::message("Removing old website...", 1);
    $sys::rrmdir($paths->dist);
    $sys::message("✔ Old Website removed  \n", 2, true);
}


$sys::message("Loading pages...", 1);

if(!is_dir($paths->pages)){
    $sys::message("Pages couldn't be loaded: $paths->pages doesn't exist.\n", 3, true);
    exit(1);
}

$pages = $sys::getDir($paths->pages, $availableExtensions);
$pagesCount = count($pages);

if($pagesCount <= 0) {
    $sys::message("Pages couldn't be loaded: $paths->pages is empty.\n", 3, true);
    exit(1);
}

$sys::message("✔ Pages loaded.\n", 2, true);


$sys::message("Loading languages...", 1);

if(!is_dir($paths->languages)){
    $sys::message("Languages couldn't be loaded: $paths->languages doesn't exist.\n", 3, true);
    exit(1);
}

$languages = array_map(function ($element) use ($build) {
    return $build::removeExtension($element);
}, $sys::getDir($paths->languages, ["json"]));

if(count($languages) <= 0) {
    $sys::message("$paths->languages is empty, ingoring...\n", 1, true);
} else{
    $sys::message("✔ Languages loaded.\n", 2, true);
}

$sys::message("Creating pages...\n", 1);

foreach($pages as $currentPage){
    $pageName = $build::removeExtension($currentPage);

    $sys::message("\tCreating $pageName...", 1);
    
    foreach($languages as $lang){
        $currentDir = $paths->dist . $lang . "/";

        if (!is_dir($currentDir)) {

            if (!mkdir($currentDir, 0755, true)) {
                $sys::message("Couldn't create $currentDir", 3, true);
                exit(1);
            }
        
        }

        $pageContents = $build::getPage(
            require $settings->global_variables ??  is_file(ROOT_DIR . "/src/variables.php") ? ROOT_DIR . "/src/variables.php" : [],
            $template,
            $paths->pages . $currentPage,
            $pageName,
            json_decode(file_get_contents($paths->languages . $lang . ".json"), true),
            $lang
        );

        if($settings->pages->minify ?? false) {
            $pageContents = $build::minify($pageContents);
        }
        
        $directory = dirname($currentDir . $pageName);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($currentDir . $pageName. ".html", $pageContents);

        $defaultLang = $settings->languages->default ?? null;
        if($lang === $defaultLang){
            $extension = "html";
            
            if($settings->languages->redirectOnDefault ?? false){
                $pageContents = <<<EOD
                <?php
                    http_response_code(301);
                    header("Location: /$defaultLang/$pageName.html");
                ?>
                $pageContents
                EOD;

                $extension = "php";
            }

            $directory = dirname($paths->dist . $pageName . '.' . $extension);

            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            file_put_contents($paths->dist . $pageName . '.' . $extension, $pageContents);

        }

    }

    $sys::message("\t✔ $pageName.html created  \n", 2, true);
}

echo "\e[" . $pagesCount+1 . "A";
$sys::message("✔ Pages created.", 2, true);
echo "\e[$pagesCount" . "B\n";


$sys::message("Copying public directory...", 1);

$sys::rcopy($paths->public, $paths->dist);

$sys::message("✔ Public directory copied.\n", 2, true);

if(isset($arguments["seo"]) || $settings->seo->always ?? false){
    $seoSettings = $settings->seo ?? null;
    $seoPath = (($seoSettings->buildInPublic ?? false) ? $paths->public : $paths->dist);

    $seo = new SEO($seoSettings->url ?? null);

    $sys::message("Generating sitemaps...\n", 1);

    foreach ($languages as $lang) {

        $sys::message("\tCreating $lang's sitemap...", 1);

        foreach ($sys::getDir("$paths->dist/$lang/") as $page) {
            $pageName = str_replace($paths->dist, '', $page);

            foreach ($availableExtensions as $ext) {
                $actualPageName = $paths->pages . $build::removeExtension($pageName) . "." . $ext;
                
                if (file_exists($actualPageName)) {
                    break;
                }
            }

            $pageSettings = $seoSettings->sitemap->pages->{$build::removeExtension($pageName)} ?? null;

            $args = [
                "$lang/" . $pageName,
                date("Y-m-d", filemtime($actualPageName)),
                $pageSettings->priority ?? 0.5,
                $pageSettings->changefreq ?? null
            ];

            $seo->addPage(...$args);
        }

        $sitemapName = str_replace("#{{lang}}", $lang, $seoSettings->sitemap->structure ?? "sitemap-#{{lang}}.xml");
        $sitemaps[] = $sitemapName;

        file_put_contents(
            $seoPath . $sitemapName,
            $seo->getSitemap()
        );

        $seo->resetSitemap();
        $sys::message("\t✔ $lang's sitemap created. \n", 2, true);
    }

    $sys::message("Creating robots.txt...", 1);

    file_put_contents(
        $seoPath . "robots.txt",
        $seo->getRobots(
            array_map(function ($element) { return "/$element/"; }, $languages),
            $seoSettings->robots->disallow ?? [],
            array_map(function ($element) use($seo) { return $element; }, $sitemaps)
        )
    );

    $sys::message("✔ robots.txt created.\n", 2, true);
}

$sys::message("Done! Enjoy your website\n\n", 1);
echo "Check out https://github.com/SebaOfficial/ for more.\n";