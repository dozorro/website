<?php

namespace App\Classes;

/**
 * Class Lang
 * @package App\Classes
 */
final class AutoFormat
{
    public static function format($message)
    {
        $message = trim(strip_tags($message));
        preg_match_all('|http(.*)[\s\n\r\t]|iU', $message."\n", $result, PREG_PATTERN_ORDER);

        if(isset($result[0]) && !empty($result[0])) {
            foreach($result[0] AS $link) {
                $link = trim($link);
                $message = str_replace($link, "<a href=\"{$link}\" target=\"_blank\">{$link}</a>", $message);
            }
        }

        return nl2br(trim($message));
    }
}
