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
    $select_before = "";
    $select_after="";
    if (isset ($public_access)) {
        $select_after = "<div id='information'>$public_access</div>";
    }
    $select_after.="<div id='presentation'>".Kohana::lang('home.presentation')."</div>";
    include "select.php";
    
    
    // DISPLAY OPEN EVALUATIONS
    /*if (isset($evaluations)) {
        include APPPATH."/views/data/inc_description.php";
        echo "<ul class='chapter_contents'>";
        foreach ($evaluations as $item) {
            echo "<li>";
            echo "<a class = 'subchapter_subtitle' href=\"".html::url($item['activity']['link'])."\">".$item['activity']['text']."</a>";
            echo "<a class = 'subchapter_title' href=\"".html::url($item['evaluation']['link'])."\">".$item['evaluation']['text']."</a>";
            if (isset ($item['description'])) {
                echo $item['description'];
            }
            if (isset($item['formSessions'])) {
                echo "<div class='subchapter'>".$item['formSessions_title']."</div>";
                echo "<ul class='subchapter_contents'>";
                foreach ($item['formSessions'] as $subitem) {
                    echo "<li><a href=\"".html::url($subitem['link'])."\">".$subitem['text']."</a></li>";
                }
                echo "</ul>";
            }
            if (isset($item['interviewSessions'])) {
                echo "<div class='subchapter'>".$item['interviewSessions_title']."</div>";
                echo "<ul class='subchapter_contents'>";
                foreach ($item['interviewSessions'] as $subitem) {
                    echo "<li><a href=\"".html::url($subitem['link'])."\">".$subitem['text']."</a></li>";
                }
                echo "</ul>";
            }
            echo "</li>";
        }
        echo "</ul>";
        if (isset ($public_access)) {
            echo "<div id='information'>$public_access</div>";
        }
    }

    // DISPLAY CLOSED EVALUATIONS
    if (isset($evaluationsOver)) {
        include APPPATH."/views/data/inc_description.php";

        echo "<ul class='chapter_contents'>";
        foreach ($evaluationsOver as $item) {
            echo "<li>";
            echo "<a class = 'subchapter_subtitle' href=\"".html::url($item['activity']['link'])."\">".$item['activity']['text']."</a>";
            echo "<a class = 'subchapter_title' href=\"".html::url($item['link'])."\">".$item['text']."</a>";
            if (isset ($item['description'])) {
                echo $item['description'];
            }
            echo "</li>";
        }
        echo "</ul>";
        if (isset ($public_access)) {
            echo "<div id='information'>$public_access</div>";
        }
    }

    // DISPLAY ACTIVITIES
    if (isset($activities)) {
        $items = $activities;
        include APPPATH."/views/activity/list.php";
        if (isset ($public_access)) {
            echo "<div id='information'>$public_access</div>";
        }
    }*/
?>
