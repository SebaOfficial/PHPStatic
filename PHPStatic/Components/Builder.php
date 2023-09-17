<?php
/**
 * Builder class.
 * 
 * @package PHPStatic
 * @author Sebastiano Racca
 */

namespace PHPStatic;

class Builder {

    /**
     * Minifies an HTML string.
     * 
     * @param string $buffer   The HTML to be minified.
     */
    public static function minify(string $buffer) {
        return preg_replace(
            [
                '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
                '/[^\S ]+\</s',     // strip whitespaces before tags, except space
                '/(\s)+/s',         // shorten multiple whitespace sequences
                '/<!--(.|\s)*?-->/' // Remove HTML comments
            ],
            [
                '>',
                '<',
                '\\1',
                ''
            ],
            $buffer
        );
    }

    /**
     * Removes the file extension.
     * 
     * @param string $string   The file name.
     * 
     * @return string          The file name without the exitension.
     */
    public static function removeExtension(string $string): string{
        $lastDotPosition = strrpos($string, ".");

        if ($lastDotPosition !== false) {
            return substr($string, 0, $lastDotPosition);
        }

        return $string;
    }

    private static function replacePlaceholders($match, $texts){
        $keys = explode('.', $match[1]);
    
        foreach ($keys as $key) {
            if (isset($texts[$key])) {
                $texts = $texts[$key];
            } else {
                return $match[0]; // Return the original placeholder if key doesn't exist
            }
        }
    
        return $texts;
    }

    /**
     * Returns the body of a page
     * 
     * @param string $page         The page name (without extension).
     * @param string $lang         The language of the page.
     * 
     * @return string              The actual page.
     */
    public static function getPage(array $globalVariables, string $templateContents, string $pagePath, string $pageName, array $texts, string $lang): string {
        $fileExtension = strtolower(pathinfo($pagePath, PATHINFO_EXTENSION));

        if ($fileExtension === "php") {

            ob_start();
            include $pagePath;
            $content = ob_get_clean();

        } else if($fileExtension === "md"){

            $parsedown = new \Parsedown();
            $content = $parsedown->text(file_get_contents($pagePath));

        }

        $content = preg_replace_callback('/#{{(.*?)}}/', function ($match) use ($texts, $pageName) {
            return self::replacePlaceholders($match, $texts[$pageName]);
        }, $content ?? file_get_contents($pagePath));

        $content = preg_replace_callback('/#{{(.*?)}}/', function ($match) use ($texts) {
            return self::replacePlaceholders($match, $texts);
        }, $content);

        
        $template = preg_replace_callback('/#{{(.*?)}}/', function ($match) use ($texts, $pageName) {
            return self::replacePlaceholders($match, $texts[$pageName]);
        }, $templateContents);
        
        $template = preg_replace_callback('/#{{(.*?)}}/', function ($match) use ($texts) {
            return self::replacePlaceholders($match, $texts);
        }, $template);

        $fullPage = strtr(
            $template,
            array_merge(
                [
                    "#{{CURRENT_PAGE}}" => $pageName,
                    "#{{CURRENT_PAGE_NO_INDEX}}" => $pageName == "index" ? "" : $pageName,
                    "#{{CURRENT_LANGUAGE}}" => $lang,
                    "#{{PAGE_CONTENTS}}" => $content,
                ],
                $globalVariables
            )
        );

        $dom = new \DOMDocument();
        @$dom->loadHTML($fullPage);

        $styleElements = $dom->getElementsByTagName('style');

        // Get the <head> element
        $head = $dom->getElementsByTagName('head')->item(0);

        if ($styleElements->length > 0 && $head) {
            // Loop through each <style> element and append it to the <head>
            foreach ($styleElements as $styleElement) {
                $head->appendChild($styleElement);
            }

            // Save the modified HTML back to a file
            $fullPage = $dom->saveHTML();
        }

        return $fullPage;
    
    }
}