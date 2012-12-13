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
    require_once "inc_text.php";

    function displayItem($item, $table = true) {
        if ($item['type'] == 'form_separator') {
            if ($table) {
                echo "<tr><td class='display_separator";
                if (isset($item['class']))
                    echo " ".$item['class'];
                echo "' colspan='2'>";
            }

            if (isset($item['sub_separator'])&&$item['sub_separator']) {
                $className = "form_sub_separator";
            } else {
                $className = "form_separator";
            }

            echo "<div class='$className'>".convert($item['value'])."</div>";
            if (isset($item['description'])) {
                echo "<div class='${className}_description'>".convert(Kohana::lang($item['description']))."</div>";
            } else if (isset($item['translated_description'])) {
                echo "<div class='${className}_description'>".convert($item['translated_description'])."</div>";
            }
            if ($table)
                echo "</td></tr>";
            return;
        }

        if ($table) {
            echo "<tr><td class='display_label";
            if (isset($item['class']))
                echo " ".$item['class'];
            echo "'>";
        }
        if (isset($item['label'])) {
            echo convert(Kohana::lang($item['label']));
        } else if (isset($item['translated_label'])) {
            echo convert($item['translated_label']);
        }
        if ($table) {
            echo "</td><td class='display_value";
            if (isset($item['class']))
                echo " ".$item['class'];
            echo "'>";
        } else
            echo " ";

        switch ($item['type']) {
            case 'text':
                echo convert($item['value']);
                break;
            case 'long_text':
                echo "<span class='long_text'>".convert($item['value'])."</span>";
                break;
            case 'images':
                echo html::image(array("src"=>$item['path']));
                break;
            case 'link':
                echo "<a href=\"".html::url($item['link'])."\">".convert($item['value'])."</a>";
            case 'separator':
                echo "&nbsp;";
                break;
            case 'list':
                echo "<ul>";
                foreach ($item['values'] as $value) {
                    echo "<li>";
                    foreach ($value as $value_item) {
                        displayItem($value_item, false);
                    }
                    echo "</li>";
                }
                echo "</ul>";
                break;
            case 'color':
                if (isset($item['value']))
                    echo $item['value'];
                echo "<div class='color' style='background-color:".$item['code']."'>&nbsp;</div><br/>";
                break;
            case 'file':
                echo "<div class='file_icon'><a href='".html::url($item['path'])."'>".html::image('images/file.png')."</a>";
                if (isset($item['value']))
                    echo "<a href='".html::url($item['path'])."'>".$item['value']."</a>";
                echo "</div>";

                break;
        }
        if ($table)
            echo "</td></tr>";
        else
            echo "<br/>";
    }
// ?>