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
    require_once APPPATH."/views/data/inc_edit.php";
    echo form::open_multipart(null, array ('id'=>"form_question_triggers_$id"));
    echo "<div class='item_content' id = 'item_content_$id'>";
    echo form::hidden($formId,'1'); // Form id
    echo "<table class='toucan_edition'>";
    displayEditItem($data[0], $errors); // enabled
    echo "</table>";
    if ($showContent)
        echo "<table id='question_triggers_content_$id' class='toucan_edition'>";
    else
        echo "<table id='question_triggers_content_$id' style='display:none;' class='toucan_edition'>";
    displayEditItem($data[1], $errors); // question choice
    $privateItem = $data[2];
    unset($data[0]);
    unset($data[1]);
    unset($data[2]);
    echo "</table>";
    echo "<table id='question_triggers_choices_$id' class='toucan_edition'>";
    include "triggerChoices.php";
    echo "</table>";
    echo "<table class='toucan_edition'>";
    displayEditItem(array('type'=>'separator'), $errors); // question choice
    displayEditItem($privateItem, $errors); // question choice
    echo "</table>";
    echo "<table class='toucan_edition'><tr><td class='edition_label'></td><td class='edition_entry'>";
    echo "<div class='question_actions'>";
    echo form::button(array ('type'=>'button', 'name'=>"button_cancel_question"), Kohana::lang($cancel),"onClick=\"cancelEdit('$id')\" class=\"toucan_button\"");
    echo form::button(array ('type'=>'button', 'name'=>"button_save_question"), Kohana::lang($save),"onClick=\"saveTriggers('$id')\" class=\"toucan_button\"");
    echo "</div>";
    echo "</td></tr></table>";
    echo form::close();
?>
<script type="text/javascript">
    Event.observe("triggers_enable_<?php echo $id;?>","change", function() {
        element1 = $('question_triggers_content_<?php echo $id;?>');
        element2 = $('question_triggers_choices_<?php echo $id;?>');
    	if ($('triggers_enable_<?php echo $id;?>').checked) {
        	element1.show();
        	element2.show();
    	} else {
    		element1.hide();
            element2.hide();
    	}
        });
    Event.observe("triggers_choice_<?php echo $id;?>","change", function(){
        id = $('triggers_choice_<?php echo $id;?>').value;
        elementName = 'question_triggers_choices_<?php echo $id;?>';
        new Ajax.Updater(elementName, '<?php echo html::url($choicesUrl)."/".$id;?>/'+id , {
                        method: 'post',
                        });
        });
</script>
