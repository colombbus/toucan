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
          try {
              abortFetch();
          }
          catch (error)
          {}
          document.location='<?php echo html::url($updateUrl);?>/'+$F('category');
      }
<?php
    if (isset($showUrl)) {
?>
    function showCategory() {
          try {
              abortFetch();
          }
          catch (error)
          {}
          document.location='<?php echo html::url($showUrl);?>/'+$F('category');
      }
<?php
    }
?>
</script>
<?php
    $values = array();
    $description = "";
    foreach ($categories as $id=>$category) {
        $values[$id] = htmlspecialchars($category['title'],ENT_QUOTES, "UTF-8");
        if ($id == $selectedCategory)
            $description = nl2br(htmlspecialchars($category['description'],ENT_QUOTES, "UTF-8"));
    }
    $definition = array('name'=>'category','onChange'=>'changeCategory()', 'class'=>'category_selection');
    if (isset($showUrl)) {
        echo "<div id='category_link'><a href='javascript:showCategory();'>".html::image(array('src' => Kohana::config('toucan.images_directory')."/category.png", 'title' => Kohana::lang("category.show")))."</a></div>";
        echo "<div id='category_link_list'>";
        
    }
    echo form::dropdown($definition,$values,$selectedCategory);
    if (isset($showUrl)) {
        echo "</div>";
        
    }
    if (strlen(trim($description))>0)
        echo "<div id='category_description'>$description</div>";
?>
