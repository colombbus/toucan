<?php
    function convert($text) {
        return str_replace("\n", "<br/>", htmlspecialchars($text,ENT_QUOTES, "UTF-8"));
    }

?>
