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

    if (isset($noItems)) {
        echo "<tr><td class='no_item'>".convert(Kohana::lang($noItems))."</td></tr>";
    } else {
        $colsCounted = false;
        $even = true;

        foreach ($items as $item) {
            if (!$colsCounted)
                $colsNumber = count($item['info']);
            echo "<tr class='table_row'>";
            foreach ($item['info'] as $info) {
                echo "<td ";
                if ($even)
                    echo "class='table_cell even' ";
                else
                    echo "class='table_cell odd' ";
                echo "><a href='".html::url($item['link'])."'>".convert($info)."</a></td>";
            }
            if (isset($icons)) {
                    foreach ($icons as $icon) {
                    echo "<td ";
                    if ($even)
                        echo "class='table_cell even table_actions'>";
                    else
                        echo "class='table_cell odd table_actions'>";
                    if (isset($icon['confirm'])) {
                        echo "<a onClick=\"if (window.confirm('".addslashes(Kohana::lang($icon['confirm']))."')) document.location='".html::url($icon['action'].$item['id'])."'\">".html::image(array('src' => $icon['src'], 'title' => Kohana::lang($icon['text'])))."</a>";
                    } else {
                        echo "<a href='".html::url($icon['action'].$item['id'])."'>".html::image(array('src' => $icon['src'], 'title' => Kohana::lang($icon['text'])))."</a>";
                    }
                    echo "</td>";
                    if (!$colsCounted)
                        $colsNumber+=count($icons);
                }
            }
            echo "</tr>";
            $colsCounted = true;
            $even = !$even;
        }
        echo "<tr><td colspan='$colsNumber' class='table_pagination'>".$pagination."</td></tr>";
?>
    <script type="text/javascript">
    initRows();
    </script>
<?php
        }
?>

