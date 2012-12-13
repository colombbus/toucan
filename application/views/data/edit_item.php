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
    include "inc_edit.php";

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

    include "inc_conditional.php";

    echo "<tr><td></td><td class='edition_actions'>";
    echo "<div class='item_actions'>";
    echo form::button(array ('type'=>'button', 'name'=>"button_cancel_item"), Kohana::lang($cancel),"onClick=\"cancelEdit('$id')\" class=\"toucan_button\"");
    echo form::button(array ('type'=>'button', 'name'=>"button_save_item"), Kohana::lang($save),"onClick=\"saveItem('$id')\" class=\"toucan_button\"");
    echo "</div>";
    echo "</td></tr>";
    echo "</table>";

    echo form::close();

?>