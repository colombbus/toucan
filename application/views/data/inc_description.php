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
<script type="text/javascript">
    
    function openTabs() {
        new Ajax.Request('<?php echo html::url("axTab/open")?>',
                    {
                        method:'post',
                        onSuccess: function(transport){
                            $$('.tab_description').each(function(element){
                                element.show();
                            });
                            $('tabs_open').hide();
                            $('tabs_close').show();
                            $('tabs').addClassName('tabs_expanded');
                            $('inner_content_narrow').addClassName('tabs_expanded');
                        }
                    });
    }
    
    function closeTabs() {
        new Ajax.Request('<?php echo html::url("axTab/close")?>',
                    {
                        method:'post',
                        onSuccess: function(transport){
                            $$('.tab_description').each(function(element){
                                element.hide();
                            });    
                            $('tabs_open').show();
                            $('tabs_close').hide();
                            $('tabs').removeClassName('tabs_expanded');
                            $('inner_content_narrow').removeClassName('tabs_expanded');
                        }
                    });
    }
    
</script>

<?php
    $level = 1;
    if (!isset($pathType)) {
        $pathType = "";
    }
    if (isset($path)) {
        foreach ($path as $item) {
            if (isset ($item['link']))
                echo "<div id='path_item_$level' class='$pathType'><a href='".html::url($item['link'])."'>".htmlspecialchars($item['text'],ENT_QUOTES, "UTF-8")."</a>";
            else
                echo "<div id='path_item_$level' class='current_path $pathType'>".htmlspecialchars($item['text'],ENT_QUOTES, "UTF-8");
            if ( $level==1 && isset($title_logo)) {
                echo "<div id='title_logo'>".html::image(array("src"=>$title_logo))."</div>";
            }
            echo "</div>\n";
            $level++;
        }
    }
    if (isset($title)) {
        echo "<div id='path_item_$level' class='current_path $pathType'>";
        if (isset($title_icon)) {
            echo "<div id='title_icon'>".html::image(array("src"=>$title_icon))."</div>";
        }
        if (isset($title_steps)) {
            echo "<ul id='title_steps'>";
            for($i=1; $i<=$title_steps['max'];$i++) {
                if ($i == $title_steps['current']) {
                    echo "<li class='title_step_current'>".$i."</li>";
                } else {
                    echo "<li class='title_step'>".$i."</li>";
                }
            }
            echo "</ul>";
        }
        echo htmlspecialchars($title,ENT_QUOTES, "UTF-8");
        if ( $level==1 && isset($title_logo)) {
            echo "<div id='title_logo'>".html::image(array("src"=>$title_logo))."</div>";
        }
        echo "</div>\n";
    }
    echo "<div id='inner_background'>";
    if (isset($tabs)&&count($tabs)>0) {

        echo "<div id='tabs'";
        if (Kohana::config('toucan.tabs_expanded')) {
            echo " class='tabs_expanded' "; 
        }
        echo " >\n";
        foreach ($tabs as $tabData) {
            if (isset($tabData['current'])) {
                echo "<div class='tab current_tab'>";
            } else {
                echo "<div class='tab'>";
            }
            echo "<a href=\"".html::url($tabData['link'])."\">".html::image(array("src"=>$tabData['image'], "title"=>Kohana::lang($tabData['text'])))."<span class='tab_description'";
            if (!Kohana::config('toucan.tabs_expanded')) {
                echo " style='display:none' "; 
            }
            echo ">".htmlspecialchars(Kohana::lang($tabData['text']),ENT_QUOTES, "UTF-8")."</span></a></div>";
        }
        echo "<div id='bottom_tabs'>";
        
        /*echo "<div id='help_trigger' class='tab'><a onClick='javascript:toggleHelp()'>";
        echo html::image(array("src"=>Kohana::config('toucan.images_directory')."/help.png"));
        echo "<span class='tab_description' ";
        if (!Kohana::config('toucan.tabs_expanded')) {
                echo " style='display:none' "; 
            }
        echo ">".Kohana::lang('help.main')."</span></a></div>";
        */
        echo "<div id='tabs_open'  class='tab'";
        if (Kohana::config('toucan.tabs_expanded')) {
            echo " style='display:none' ";
        }
        echo ">";
        echo "<a href='javascript:openTabs()'>";
        echo html::image(array("src"=>Kohana::config('toucan.images_directory')."/open_tabs.png", "title"=>Kohana::lang("menu.open_tabs")))."</a></div>";
        echo "<div id='tabs_close' class='tab'";
        if (!Kohana::config('toucan.tabs_expanded')) {
            echo " style='display:none' ";
        }
        echo ">";
        echo "<a href='javascript:closeTabs()'>";
        echo html::image(array("src"=>Kohana::config('toucan.images_directory')."/close_tabs.png", "title"=>Kohana::lang("menu.close_tabs")))."<span class='tab_description'>".htmlspecialchars(Kohana::lang("menu.close_tabs"),ENT_QUOTES, "UTF-8")."</span></a></div>";
        echo "</div>";
        echo "</div>";
        echo "<div id='inner_content_narrow'";
        if (Kohana::config('toucan.tabs_expanded')) {
            echo " class='tabs_expanded' "; 
        }
        echo " >";

    } else {
        echo "<div id='inner_content_wide'>";
    }
    if (isset($errorMessage)) {
        echo "<div id='error_message'>".htmlspecialchars($errorMessage,ENT_QUOTES, "UTF-8")."</div>";
    }
    if (isset($message))
        echo "<div id='message'>".htmlspecialchars($message,ENT_QUOTES, "UTF-8")."</div>";
    if (isset($description)) {
        echo "<div class='description'>";
        echo htmlspecialchars($description,ENT_QUOTES, "UTF-8");
        echo "<a title=\"".Kohana::lang('help.main')."\" id ='help_trigger' onClick='javascript:toggleHelp()'></a>";
        echo "</div>";
    }
    echo "<div id='help' style='display:none;'></div>";
?>