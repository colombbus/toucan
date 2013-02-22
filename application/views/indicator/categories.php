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
      function changeCategory() {
            document.location='<?php echo html::url($updateUrl);?>/'+$F('category');
      }
</script>
<?php
    $values = array();
    $description = "";
    foreach ($categories as $id=>$category) {
        $values[$id] = htmlspecialchars($category['name'],ENT_QUOTES, "UTF-8");
        if ($id == $selectedCategory)
            $description = htmlspecialchars($category['description'],ENT_QUOTES, "UTF-8");
    }
    $definition = array('name'=>'category','onChange'=>'changeCategory()', 'class'=>'category_selection');
    echo form::dropdown($definition,$values,$selectedCategory);
    if (strlen(trim($description))>0)
        echo "<div id='category_description'>$description</div>";
?>
