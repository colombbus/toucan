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
    echo "<li class='form' id='choice_container_".$choice['index']."'>\n";
    echo "<div class='handle_choice'></div>";
    echo "<div class='choice_content'>";
    echo "<table class='toucan_edition'>";
    foreach ($choice['data'] as $item) {
        displayEditItem($item, array(), true);
    }
    echo "</table>";
    echo "<div class=\"choice_action\">";
    echo form::button(array ('type'=>'button', 'name'=>"button_delete_choice_".$choice['index']), Kohana::lang($deleteChoice),"onClick=\"deleteChoice('$id', 'choice_container_".$choice['index']."')\" class=\"toucan_button\"");
    echo "</div>";
    echo "</div>";
    echo "</li>\n";
?>
<script>
    Sortable.create('item_choices_content_<?php echo $id;?>');
</script>