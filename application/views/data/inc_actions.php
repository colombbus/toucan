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
    function displayAction($action) {
        global $buttonIndex;
        switch ($action['type']) {
            case 'button':
                if (isset($action['url'])) {
                    if (isset($action['newWindow'])) {
                        echo form::button(array ('type'=>'button', 'name'=>"button".$buttonIndex++), Kohana::lang($action['text']),"onClick=\"window.open('".html::url($action['url'])."', 'newwindow')\" class=\"toucan_button\"");
                    } else {
                        echo form::button(array ('type'=>'button', 'name'=>"button".$buttonIndex++), Kohana::lang($action['text']),"onClick=\"document.location='".html::url($action['url'])."'\" class=\"toucan_button\"");
                    }
                }
                else if (isset($action['js']))
                    echo form::button(array ('type'=>'button', 'name'=>"button".$buttonIndex++), Kohana::lang($action['text']),"onClick=\"".$action['js']."\" class=\"toucan_button\"");
                break;
            case 'button_confirm':
                echo form::button("button".$buttonIndex++, Kohana::lang($action['text']),"onClick=\"if (window.confirm('".addslashes(Kohana::lang($action['confirm']))."')) document.location='".html::url($action['url'])."'\" class= \"toucan_button\"");
                break;
            case 'submit':
                echo form::button(array ('type'=>'submit', 'name'=>"button".$buttonIndex++), Kohana::lang($action['text']), "class = \"toucan_button\"");
                break;
            case 'cancel':
                echo form::button(array ('type'=>'button', 'name'=>"button".$buttonIndex++), Kohana::lang($action['text']),"onClick=\"history.back()\" class = \"toucan_button\"");
                break;
        }
    }

    function displayQuickAction($action) {
        if (isset($action['url']))
            echo "<a href='".html::url($action['url'])."' class='quick_action'>".html::image(array("src"=>$action['image'], "title"=>$action['text']))."</a>";
        else if (isset($action['js']))
            echo "<a onClick='".$action['js']."' class='quick_action'>".html::image(array("src"=>$action['image'], "title"=>$action['text']))."</a>";
    }

    
    $buttonIndex = 0;
    if (isset($actions)||isset($actions_back)||isset($quickActions)) {
        echo "<div id='actions'>\n";
            if (isset($actions_back)) {
                echo "<div id='actions_left'>\n";
                    foreach ($actions_back as $action) {
                        displayAction($action);
                    }
                echo "</div>";
            }
            if (isset($actions)) {
                echo "<div id='actions_right'>\n";
                    foreach ($actions as $action) {
                        displayAction($action);
                    }
                echo "</div>";
            }
            if (isset($quickActions)&&(sizeof($quickActions)>0)) {
                echo "<div id='quick_actions'>\n";
                    foreach ($quickActions as $quickAction) {
                        displayQuickAction($quickAction);
                    }
                echo "</div>";
            }
        echo "<div class='float_end'></div>";
        echo "</div>\n";
    }
    if (isset($INCLUDE_BEFORE_CLOSING)) {
        echo $INCLUDE_BEFORE_CLOSING;
    }
    
    echo "</div>"; // end of div inner_content
    echo "<div class='float_end'></div>";
    echo "</div>"; // end of div inner_background
?>