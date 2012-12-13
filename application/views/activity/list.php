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
    function recDisplayItem($item, $level=0, $itemIcon = null) {
/*        echo "<ul class=\"hierarchical item_level_$level\">";
        foreach ($item['info'] as $name=>$info) {
            echo "<li class=\"item_info_$name\"><a href='".html::url($item['link'])."'>".$info."</a></li>";
        }
        if (isset ($item['sub_items'])) {
            foreach ($item['sub_items'] as $subitem) {
                recDisplayItem($subitem, $level+1);
            }
        }
        echo "</ul>";*/
        foreach ($item['info'] as $name=>$info) {
            echo "<a href='".html::url($item['link'])."' class='hierarchical item_level_$level'>";
            if (($level == 0) && isset($itemIcon)) {
                echo html::image(array("src"=>$itemIcon, "class"=>'hierarchical_image'));
            }
            echo $info."</a>";
        }
        if (isset ($item['sub_items'])) {
            foreach ($item['sub_items'] as $subitem) {
                recDisplayItem($subitem, $level+1);
            }
        }
    }
    
    include APPPATH."/views/data/inc_description.php";
    
    if (isset($noItems)) {
        echo Kohana::lang($noItems);
    } else {
        $colsNumber = false;
        if (!isset($itemIcon))
            $itemIcon = null;
        foreach ($items as $item) {
            echo "<div class='hierarchical_group'>";
            recDisplayItem($item,0,$itemIcon);
            echo "</div>";
            if (!$colsNumber)
                $colsNumber = count($item['info']);
        }
        if (isset ($pagination)) {
            echo "<tr><td colspan='$$colsNumber'>".$pagination."</td></tr>";
        }
    }

    include APPPATH."/views/data/inc_actions.php";
?>