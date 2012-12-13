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
    include "inc_description.php";
    include "inc_edit.php";

    echo form::open_multipart(null, array('id'=>$formId));
    echo form::hidden($formId,'1'); // Form id
    if (!isset($errors))
        $errors = array();
    if (isset($errors['main']))
        echo "<div id='error_main'>".$errors['main']."</div>";
    echo "<table class='toucan_edition'>";
    echo "<tr><td class='edition_margin' colspan='2'></td></tr>";
    $requiredTextRequired = false;
    foreach ($data as $item) {
        displayEditItem($item, $errors);
         if (isset($item['required'])&&$item['required']) {
             $requiredTextRequired = true;
         }
    }
    echo "<tr><td class='edition_margin' colspan='2'></td></tr>";
    echo "</table>";
    if ($requiredTextRequired) {
        if (!isset($requiredText))
            $requiredText = Kohana::lang('main.data_required');
        echo "<div id='required_text'><span class='required'>*</span>$requiredText</div>";
    }
    
    include "inc_actions.php";

    echo form::close();

    include "inc_conditional.php";
?>