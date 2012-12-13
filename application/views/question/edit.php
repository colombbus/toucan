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
    include APPPATH."/views/data/inc_edit.php";
    //$order = $item['order'];
    if (isset ($item['id'])) {
        $id = $item['id'];
    } else {
        $id = "new";
    }
    echo form::open_multipart(null, array ('id'=>"form_item_$id"));
    if (isset ($item['id'])) {
        echo form::hidden("id",$item['id']); // Question id
    }
    echo form::hidden($formId,'1'); // Form id
    echo "<div class='item_content' id = 'item_content_$id'>";
    echo "<table class='toucan_edition'>";
    foreach ($item['content'] as $info) {
        displayEditItem($info, $errors);
    }
    if ($displayChoices) {
        if ($showChoices)
            echo "<tr> <td class='edition_label' id = 'item_choices_label_$id'>".Kohana::lang($availableChoices)."</td><td class='question_choices' id='item_choices_$id'>";
        else
            echo "<tr> <td class='edition_label' id = 'item_choices_label_$id' style='display:none'>".Kohana::lang($availableChoices)."</td><td class='question_choices' id='item_choices_$id' style='display:none'>";
        echo "<ul class='question_choices' id = 'item_choices_content_$id'>";
        $i = 0;
        foreach ($choices as $choice) {
            echo "<li class='form' id='choice_container_".$choice['index']."'>\n";
            echo "<div class='handle_choice'></div>";
            echo "<div class='choice_content'>";
            echo "<table class='toucan_edition'>";
            foreach($choice['data'] as $item) {
                displayEditItem($item, array(),true);
            }
            if (isset($errors["choice_$i"]))
                echo "<tr><td></td><td><span class='error'>".$errors["choice_$i"]."</span></td>";
            echo "</table>";
            echo "<div class=\"choice_action\">";
            echo form::button(array ('type'=>'button', 'name'=>"button_delete_choice_".$choice['index']), Kohana::lang($deleteChoice),"onClick=\"deleteChoice('$id', 'choice_container_".$choice['index']."')\" class=\"toucan_button\"");
            echo "</div>";
            echo "</div>\n";
            echo "</li>\n";
            $i++;
        }
        echo "</ul>";
        echo form::button(array ('type'=>'button', 'name'=>"button_add_choice"), Kohana::lang($addChoice),"onClick=\"addChoice('$id')\" class=\"toucan_button\"");
        echo "</td></tr>";
        echo form::input(array('type'=>"hidden",'name'=>"item_choices_count_".$id,'id'=>"item_choices_count_".$id, 'value'=>sizeof($choices)));
    }
    echo "<tr><td></td><td class='edition_actions'>";
    echo "<div class='question_actions'>";
    echo form::button(array ('type'=>'button', 'name'=>"button_cancel_question"), Kohana::lang($cancel),"onClick=\"cancelEdit('$id')\" class=\"toucan_button\"");
    echo form::button(array ('type'=>'button', 'name'=>"button_save_question"), Kohana::lang($save),"onClick=\"saveItem('$id')\" class=\"toucan_button\"");
    echo "</div>";
    echo "</td></tr>";
    echo "</table>";

    echo "</div>";
    echo form::close();
    if ($displayChoices) {
?>
<script language = "javascript">
    //choiceCount = <?php echo sizeof($choices); ?>;
    Event.observe("type_id","change", function() {
        values = new Array(<?php
    $first = true;
    foreach ($displayValues as $value) {
        if (!$first)
            echo ",";
        echo "\"$value\"";
        $first = false;
    }
?>);
        if (values.indexOf($F("type_id"))>-1) {
            $("item_choices_label_<?php echo $id;?>").show();
            $("item_choices_<?php echo $id;?>").show();
        } else {
            $("item_choices_label_<?php echo $id;?>").hide();
            $("item_choices_<?php echo $id;?>").hide();
        }
    });
    Sortable.create('item_choices_content_<?php echo $id;?>',{handle:'handle_choice'});

</script>
<?php
    }
?>