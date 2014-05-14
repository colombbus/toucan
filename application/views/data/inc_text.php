<?php
    function convert($text, $convertUrls = false) {
        $text = str_replace("\n", "<br/>", htmlspecialchars($text,ENT_QUOTES, "UTF-8"));
        if ($convertUrls) {
            $text = preg_replace('!(\s|^)((https?://)+[a-z0-9_./?=&-]+)!i', ' <a href="$2" target="_blank">$2</a> ', $text." ");
            $text = preg_replace('!(\s|^)((www\.)+[a-z0-9_./?=&-]+)!i', '<a target="_blank" href="http://$2"  target="_blank">$2</a> ', $text." ");
        }
        return $text;
    }

?>
