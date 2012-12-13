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
    echo form::open_multipart('NULL', array('id'=>$formId));
    echo form::hidden($formId,'1'); // Form id
    echo "<table class='toucan_edition'>";
    foreach ($fileData as $item) {
        displayEditItem($item, $formErrors, true);
    }
    echo "<tr><td class='edition_label'></td><td class='edition_entry'>";
    echo "<div class=\"file_actions\">";
    echo form::button(array ('type'=>'button', 'name'=>"button_cancel_file"), Kohana::lang($cancel),"onClick=\"cancelEditFile($fileId)\" class=\"toucan_button\"");
    echo form::button(array ('type'=>'button', 'name'=>"button_submit_file"), Kohana::lang($submit), "onClick=\"saveFile($fileId)\" class = \"toucan_button\"");
    echo "</div>";
    echo "</td></tr>";
    echo "</table>";
    echo form::close();
?>