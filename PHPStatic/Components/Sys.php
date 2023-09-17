<?php
/**
 * System class.
 * 
 * @package PHPStatic
 * @author Sebastiano Racca
 */

namespace PHPStatic;

class Sys {

    /**
     * Recursevly copies the contents of a directory to another.
     * 
     * @param string $source        The source direcory.
     * @param string $destination   The destination directory.
     * 
     * @return void
     */
    public static function rcopy(string $source, string $destination): void{
        if (is_dir($source)) {
            if (!is_dir($destination)) {
                mkdir($destination, 0777, true);
            }
    
            $dirContents = scandir($source);
    
            foreach ($dirContents as $file) {
                if ($file != '.' && $file != '..') {
                    $srcFile = $source . '/' . $file;
                    $dstFile = $destination . '/' . $file;
    
                    if (is_dir($srcFile)) {
                        self::rcopy($srcFile, $dstFile);
                    } else {
                        copy($srcFile, $dstFile);
                    }
                }
            }
        } elseif (is_file($source)) {
            copy($source, $destination);
        }
    }

    /**
     * Recursevly removes the contents of a directory.
     * 
     * @param string $directory       The directory.
     * @param bool $removeDirectory   Wheter to remove the actual directory or just the contents.
     * 
     * @return void
     */
    public static function rrmdir(string $directory, bool $removeDirectory = false): void {
        $files = glob($directory . '/*');
        
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::rrmdir($file, true);
            } else {
                unlink($file);
            }
        }
        
        if($removeDirectory){
            rmdir($directory);
        }
    }

    /**
     * Displays a message to the console
     * 
     * @param string $text      The text to be displayed
     * @param int $level        The intensity level
     * @param bool $overwrite   Wheter the text should overwrite the previus text or not.
     * 
     * @return void
     */
    public static function message(string $text, int $level, bool $overwrite = false): void{
        $color = "\033[" . match ($level) {
            1 => 36, // Cyan
            2 => 32, // Green
            3 => 31,  // Red
        } . "m";
    
        if ($overwrite){
            echo "\r" . str_repeat(" ", strlen($text)) . "\r";
        }
    
        echo "$color$text\033[0m";
    }

    /**
     * Recursevly returns an array of absolute paths of the contents inside a directory.
     * 
     * @param string $path        The path to be searched.
     * @param array $extensions   The allowed file extensions.
     * 
     * @return array              The pages without the file path.
     */
    public static function getDir(string $path, array $extensions = ["*"]){
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        $files = [];
    
        foreach ($iterator as $file) {
            if ($file->isFile() && in_array("*", $extensions) || in_array($file->getExtension(), $extensions)) {
                $relativePath = str_replace($path, "", $file->getPathname());
                $files[] = $relativePath;
            }
        }
    
        return $files;
    }
}