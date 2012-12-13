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
    include APPPATH."/views/data/inc_description.php";
    
    if (isset($select_before))
        echo $select_before;
    
    if (isset($choices)) {
        foreach ($choices as $choice) {
            echo "<a class='select_choice ";
            if (isset ($selectType))
                echo $selectType;
            echo "' href='".html::url($choice['link'])."'>";
            if (isset($choice['image'])) {
                echo html::image(array("src"=>$choice['image'], "class"=>'select_image'));
            }
            echo $choice['text'];
            echo "</a>";
        }
    }

    if (isset($select_after))
        echo $select_after;

    include APPPATH."/views/data/inc_actions.php";
?>
