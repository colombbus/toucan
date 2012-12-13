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
?>
<script type="text/javascript">

    function selectAll() {
        var result = $$('.item');
        result.each(function(element){
            select(element, true);
         });
    }

    function deselectAll() {
        var result = $$('.item');
        result.each(function(element){
            deselect(element, true);
         });
    }
    
    function deselect(element, updateCheck) {
        element.removeClassName('selected');
        if (updateCheck) {
            var check = element.down('input');
            check.setValue(false);
        }
    }
    

    function select(element, updateCheck) {
        element.addClassName('selected');
        if (updateCheck) {
            var check = element.down('input');
            check.setValue(true);
        }
    }
    
    function toggleSelect(event) {
        var element = event.element();
        
        var updateCheck = (!element.hasClassName('check_item'));

        if (!element.hasClassName('item')) {
            element = element.up('.item');
        }
        if (element.hasClassName('selected'))
            deselect(element, updateCheck);
        else 
            select(element, updateCheck);
    }
    
    function initSelect() {
        $$('.item').each(function(element){
            Event.observe(element,'click',toggleSelect);
        });
    }
    
    Event.observe(window, 'load', initSelect);
</script>

<?php
    include "inc_description.php";
    require_once "inc_display.php";
    if (isset($selectAllItems)&&isset($deselectAllItems)) {
        echo form::button(array ('type'=>'button', 'name'=>"button_select_all"), Kohana::lang($selectAllItems),"onClick=\"selectAll()\" class=\"toucan_button\"");
        echo form::button(array ('type'=>'button', 'name'=>"button_deslect_all"), Kohana::lang($deselectAllItems),"onClick=\"deselectAll()\" class=\"toucan_button\"");
    }
    echo form::open_multipart(null, array('id'=>$formId));
    echo form::hidden($formId,'1'); // Form id
    echo "<ul id='items'>\n";
    if (isset($noItems)) {
        echo Kohana::lang($noItems);
    } else {
        foreach ($items as $item) {
            echo "<a class='item' id='item_".$item['id']."'\">";
            $definition = array();
            $definition['name'] = $selectName."[]";
            $definition['class'] = 'check_item';
            echo form::checkbox($definition,$item['id']).' ';
            echo form::label($selectName."[]",convert($item['name']));
            echo "<div class='item_description'>".convert($item['description'])."</div>";
            echo "</a>";
        }
    }
    echo "</ul>\n";
    include "inc_information.php";
    include "inc_actions.php";
    echo form::close();

?>
