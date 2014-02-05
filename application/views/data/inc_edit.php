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
    
    global $calendarIndex;
    $calendarIndex=0;
    
    function displayEditItem($item,$errors = array(), $table = true) {
        global $calendarIndex;

        if ($item['type'] == 'form_separator') {
            if ($table) {
                echo "<tr ";
                if (isset ($item['name']))
                    echo "id='form_".$item['name']."' ";
                if (isset ($item['hidden'])&&$item['hidden'])
                    echo "style ='display:none' ";
                echo ">";
                echo "<td class='edition_separator";
                if (isset($item['class']))
                    echo " ".$item['class'];
                echo "' colspan='2'>";
            }
            
            if (isset($item['sub_separator'])&&$item['sub_separator']) {
                $className = "form_sub_separator";
            } else {
                $className = "form_separator";
            }
            echo "<div class='$className'>".htmlspecialchars($item['value'],ENT_QUOTES, "UTF-8")."</div>";
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
            echo "<tr ";
            if (isset ($item['name']))
                echo "id='form_".$item['name']."' ";
            if (isset ($item['hidden'])&&$item['hidden'])
                echo "style ='display:none' ";
            echo ">";
            echo "<td class='edition_label";
            if (isset($item['class']))
                echo " ".$item['class'];
            echo "'>";
        }
        if (isset($item['required'])&&$item['required']) {
            echo "<span class='required'>*</span>";
        }
        if (!isset($item['convert_urls']))
            $item['convert_urls'] = false;
        if (isset($item['label'])) {
            echo form::label($item['name'],convert(Kohana::lang($item['label']),$item['convert_urls']));
        } else if (isset($item['translated_label'])) {
            echo form::label($item['name'],convert($item['translated_label'],$item['convert_urls']))." ";
        }
        if ($table) {
            echo "</td><td class='edition_entry";
            if (isset($item['class']))
                echo " ".$item['class'];
            echo "'>";
        }
        $definition = array();
        if (isset($item['name']))
            $definition['name'] = $item['name'];
        if (isset($item['id']))
            $definition['id'] = $item['id'];
        if (isset($item['disabled'])&&$item['disabled'])
            $definition['disabled'] = 'true';
        switch ($item['type']) {
            case 'text':
                $definition['class'] = 'text_input';
                if (isset($item['value'])) {
                    echo form::input($definition,$item['value']);
                } else {
                    echo form::input($definition);
                }
                break;
            case 'long_text':
                $definition['rows'] = '3';
                $definition['class'] = 'resizable';
                if (isset($item['value'])) {
                    echo form::textarea($definition,$item['value']);
                } else {
                    echo form::textarea($definition);
                }
                break;
            case 'check':
                if (isset($item['value']))
                    $value = $item['value'];
                else
                    $value = 1;
                if (isset($item['checked'])&&$item['checked']) {
                    echo form::checkbox($definition,$value,true);
                } else {
                    echo form::checkbox($definition,$value);
                }
                break;
            case 'file':
                if (isset($item['value'])) {
                    echo form::upload($definition,$item['value']);
                } else {
                    echo form::upload($definition);
                }
                break;
            case 'choice':
                if (isset($item['values'])) {
                    foreach ($item['values'] as $choice) {
                        $choiceDefinition['name'] = $item['name'];
                        if (isset($choice['id']))
                            $choiceDefinition['id'] = $choice['id'];
                        if (isset($item['id']))
                            $choiceDefinition['question_id'] = $item['id'];
                        $choiceDefinition['class'] = 'radio';
                        if (isset($item['value'])&&($item['value']==$choice['value'])) {
                            echo form::radio($choiceDefinition,$choice['value'],true);
                        } else {
                            echo form::radio($choiceDefinition,$choice['value']);
                        }
                        echo form::label($item['name'],$choice['label'], "class='choice'");
                        echo "<div class='end_choice'></div>";
                    }
                }
                break;
            case 'multiple_choice':
                if (isset($item['values'])) {
                    foreach ($item['values'] as $choice) {
                        $choiceDefinition['name'] = $choice['name'];
                        if (isset($item['id']))
                            $choiceDefinition['question_id'] = $item['id'];
                        if (isset($choice['id']))
                            $choiceDefinition['id'] = $choice['id'];
                        $choiceDefinition['class'] = 'checkbox';
                        if (isset($choice['checked'])&&$choice['checked']) {
                            echo form::checkbox($choiceDefinition,$choice['value'],true);
                        } else {
                            echo form::checkbox($choiceDefinition,$choice['value']);
                        }
                        if (isset($choice['label']))
                            echo form::label($choice['name'],$choice['label'], "class='choice'");
                        echo "<div class='end_choice'></div>";
                    }
                }
                break;
            case 'date':
                if (isset($item['value'])) {
                    echo form::input(array('name'=>$item['name'],'id'=>'date_'.$calendarIndex),$item['value']);
                } else {
                    echo form::input(array('name'=>$item['name'],'id'=>'date_'.$calendarIndex));
                }
                echo "<div class='calendar_trigger' onClick=\"$('popup_calendar_$calendarIndex').toggle();new Ajax.Updater('popup_calendar_$calendarIndex', '".html::url("/calendar/popup/$calendarIndex")."', { method: 'get' });\">".html::image(array("src"=>Kohana::config("toucan.images_directory")."/calendar.png"))."</div>";
                echo "<div class='calendar' id='popup_calendar_$calendarIndex' style='display:none'></div>";
                $calendarIndex++;
                break;
            case 'password' :
                $definition['class'] = 'text_input';
                if (isset($item['value'])) {
                    echo form::password($definition,$item['value']);
                } else {
                    echo form::password($definition);
                }
                break;
            case 'select':
                $values = array();
                foreach ($item['values'] as $id=>$value)
                    $values[$id] = htmlspecialchars($value,ENT_QUOTES, "UTF-8");
                if (isset($item['value'])) {
                    echo form::dropdown($definition,$values,$item['value']);
                } else {
                    echo form::dropdown($definition,$values);
                }
                break;
            case 'hidden':
                echo "<input type=\"hidden\" ";
                if (isset($definition['name']))
                    echo "name=\"".$definition['name']."\" ";
                if (isset($definition['id']))
                    echo "id=\"".$definition['id']."\" ";
                if (isset($item['value']))
                    echo "value=\"".$item['value']."\" ";
                echo ">";
                break;
            case 'separator':
                echo "&nbsp;";
                break;
            case 'group':
                foreach ($item['values'] as $value) {
                    displayItem($value, $errors);
                }
                break;
            case 'display':
                echo $item['value'];
                break;
            case 'file_contents':
                $definition['rows'] = '20';
                $definition['class'] = 'resizable';
                if (isset($item['value'])) {
                    echo form::textarea($definition,$item['value']);
                } else {
                    echo form::textarea($definition);
                }
                break;
                break;
        }
        if (isset($item['description'])) {
            echo "<div class='form_description'>".convert(Kohana::lang($item['description']),$item['convert_urls'])."</div>";
        } else if (isset($item['translated_description'])) {
            echo "<div class='form_description'>".convert($item['translated_description'],$item['convert_urls'])."</div>";
        }
        if (isset($item['name'])&&isset($errors[$item['name']]))
            echo "<div class='error'>".convert($errors[$item['name']])."</div>";
        if ($table)
            echo "</td></tr>";
        else
            echo "<br/>";
    }
?>
