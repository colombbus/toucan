<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Toucan is a web application to perform evaluation and follow-up of
 * activities.
 * Copyright (C) 2010 Colombbus (http://www.colombbus.org)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?>
<?php
    require_once "inc_display.php";
    require_once "inc_text.php";

    $id = $item['id'];
    if (isset($newItem)&&$newItem) {
        echo "<li class='item' id='item_".$id."'";
        if (isset($item['color']))
            echo "style='background-color:#".$item['color'].";'";
        echo ">";
    }
    if (isset($isDraggable) && $isDraggable)
        echo "<div class='handle'></div>";
    echo "<div class='item_actions'>";
    if (isset ($item['actions'])) {
        foreach($item['actions'] as $itemAction) {
            if (isset ($itemAction['function']))
                echo "<a href =\"javascript:".$itemAction['function']."($id)\">".convert(Kohana::lang($itemAction['text']))."</a>";
            else if (isset ($itemAction['link']))
                echo "<a href =\"".html::url($itemAction['link'].$id)."\">".convert(Kohana::lang($itemAction['text']))."</a>";
        }
    }
    echo "</div>";
    echo "<div class='item_title'>".convert($item['title']);
    echo "</div>";
    echo "<div class='item_content' id='item_content_$id' ";
    if (!isset ($showContent)|| !$showContent)
        echo "style='display:none;'";
    echo ">";
    echo "<table class='toucan_display'>";
    foreach ($item['content'] as $itemInfo) {
        displayItem($itemInfo);
    }
    echo "</table>";
    echo "</div>";
    if (isset($newItem)&&$newItem) {
        echo "</li>";
    }
?>
<?php
    if (isset($dragUpdateRequired) && $dragUpdateRequired) {
?>
<script type="text/javascript">
    createSortable();
</script>
<?php
    }
    if (!isset($newItem)||!$newItem) {
        echo "<script type='text/javascript'>\n";
        if (isset($item['color'])) {
            echo "$('item_$id').style.backgroundColor='#".$item['color']."';";
        } else {
            echo "$('item_$id').style.backgroundColor='';";
        }
        echo "</script>\n";
    }
?>