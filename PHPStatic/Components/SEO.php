<?php
/**
 * SEO Class.
 *
 * @author Sebastiano Racca
 * @package PHPSTatic
 */

namespace PHPStatic;

class SEO {
    private string $url;
    private array $pages;

    /**
     * SEO constructor.
     *
     * @param string|null $baseUrl   The base URL for the sitemap.
     */
    public function __construct (?string $baseUrl) {
        $this->url = $baseUrl ?? ("http://" . gethostname() . "/");
        $this->pages = [];
    }

    /**
     * Converts a page array to a formatted string.
     *
     * @param array $page An array representing a page.
     *
     * @return string The formatted string representing the page.
     */
    private function pageToString (array $page) :string{
        $url = "\t<url>";
        
        foreach ($page as $key => $value) {
            if ($value !== null) {
                $url .= "\n\t\t<$key>$value</$key>";
            }
        }

        return "$url\n\t</url>";
    }

    /**
     * Adds a page to the sitemap.
     *
     * @param string $location          The location of the page.
     * @param string $lastMod           The last modification date of the page.
     * @param float  $priority          The priority of the page.
     * @param string|null $changefreq   The change frequency of the page.
     * 
     * @return void
     */
    public function addPage (string $location, string $lastMod, float $priority, ?string $changefreq = null): void{
        $this->pages[] = [
            "loc" => $this->url . $location,
            "lastmod" => $lastMod,
            "changefreq" => $changefreq,
            "priority" => $priority
        ];
    }

    /**
     * Resets the sitemap by clearing all pages.
     * 
     * @return void
     */
    public function resetSitemap (): void{
        $this->pages = [];
    }

    /**
     * Gets the sitemap XML as a string.
     * 
     * @return string   The sitemap XML.
     */
    public function getSitemap (): string{
        $urls = "";
        foreach ($this->pages as $page) {
            $urls .= "\n" . $this->pageToString($page);
        }

        return <<<EOD
        <?xml version="1.0" encoding="UTF-8"?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">$urls
        </urlset>
        EOD;
    }

    public function getRobots (array $allow, array $disallow, array $sitemaps) :string{
        return "User-agent: *\n" .
            "Disallow: " . implode("\nDisallow: ", $disallow) . "\n" .
            "Allow: " . implode("\nAllow: ", $allow) . "\n" .
            "Sitemap: $this->url" . implode("\nSitemap: $this->url", $sitemaps);
    
    }

}