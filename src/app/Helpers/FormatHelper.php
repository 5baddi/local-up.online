<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FormatHelper
{
    /**
     * Format number to thousands
     * 
     * @param double $number
     * @return string
     */
    public static function thousands($number) : string
    {

        if($number > 1000){
            $x = round($number);
            $x_number_format = number_format($x);
            $x_array = explode(',', $x_number_format);
            $x_parts = array('k', 'm', 'b', 't');
            $x_count_parts = count($x_array) - 1;
            $x_display = $x;
            $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
            $x_display .= $x_parts[$x_count_parts - 1];
            
            return $x_display;
        }
    
        return $number;
    }

    /**
     * Parse array keys with ASCII code
     * 
     * @param Illuminate\Support\Collection $collection
     * @return Illuminate\Support\Collection
     */
    public static function parseArrayASCIIKey(Collection $collection) : Collection
    {
        $data = new Collection();

        // Parse Ascii code from keys
        $collection->map(function($value, $key) use ($data){
            $parsedKey = preg_replace('/[\x00-\x1F\x7F\*]/u', '', $key);
            // if($parsedKey === 'medias')
            //     return;

            $data->put($parsedKey, $value);
        });

        return $data;
    }

    /**
     * Extract short code from instagram post link
     * 
     * @param string $link
     * @return null|string
     */
    public static function extractInstagarmShortCode(string $link) : ?string
    {
        // Init 
        $match = [];

        // Extract short code
        preg_match("/^(?:https?:\/\/)?(?:www\.)?(?:instagram\.com.*\/p\/)([\d\w\-_]+)(?:\/)?(\?.*)?$/", $link, $match);

        return isset($match[1]) ? $match[1] : null;
    }
    
    /**
     * Extract all hashtags from text
     * 
     * @param string $text
     * @return array
     */
    public static function extractHashTags(string $text) : array
    {
        // Init 
        $matches = [];

        // Extract short code
        preg_match_all("/#(\\w+)/", $text, $matches);
        
        return isset($matches[1]) ? $matches[1] : [];
    }

    /**
     * Convert emoji to Unicode
     *
     * @param string $emoji
     * @return string
     */
    public static function emojiUnicode($emoji) 
    {
        $emoji = mb_convert_encoding($emoji, 'UTF-32', 'UTF-8');
        $unicode = strtoupper(preg_replace("/^[0]+/","U+",bin2hex($emoji)));
        
        return $unicode;
    }

    /**
     * Store picture locally
     *
     * @param string $pictureURL Picture URL
     * @param string $destPath Destination path
     * @return string|null
     */
    public static function storePicture(string $pictureURL, string $destPath = "influencers/instagram/pictures/") : ?string
    {
        if(!empty($pictureURL) && !is_null($pictureURL)){
            try{
                // Get picture info
                $fileDestPath = self::mergePaths($destPath, strtok(basename($pictureURL), '?'));
                // Store picture to private desc
                Storage::disk('local')->put($fileDestPath, file_get_contents($pictureURL));
    
                // Picture local path
                return $fileDestPath;
            }catch(\Exception $exception){
                // Trace
                Log::error($exception->getMessage());
            }
        }

        return $pictureURL;
    }

    /**
     * Merge several parts of URL or filesystem path in one path
     * 
     * @param string $path1
     * @param string $path2
     */
    private static function mergePaths($path1, $path2){
        $paths = func_get_args();
        $last_key = func_num_args() - 1;
        array_walk($paths, function(&$val, $key) use ($last_key) {
            switch ($key) {
                case 0:
                    $val = rtrim($val, '/ ');
                    break;
                case $last_key:
                    $val = ltrim($val, '/ ');
                    break;
                default:
                    $val = trim($val, '/ ');
                    break;
            }
        });
    
        $first = array_shift($paths);
        $last = array_pop($paths);
        $paths = array_filter($paths); // clean empty elements to prevent double slashes
        array_unshift($paths, $first);
        $paths[] = $last;
        
        return implode('/', $paths);
    }
}