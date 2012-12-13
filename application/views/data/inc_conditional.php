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

    if (isset($conditional)) {
?>
    <script type = "text/javascript">
<?php
    foreach ($conditional as $item) {
        if (isset($item['enable']))
            $enable = true;
        else
            $enable = false;
        if (isset($item['value'])) {
?>
    Event.observe("<?php echo $item['trigger']; ?>","change", function() {
       if ($F("<?php echo $item['trigger']; ?>")<?php if (isset($item['reverse'])) echo "!="; else echo "==";?>"<?php echo $item['value']?>") {
           $("<?php if (!$enable) echo "form_";?><?php echo $item['triggered'];?>").<?php if ($enable) echo "enable()"; else echo "show()";?>;
       } else {
            $("<?php if (!$enable) echo "form_";?><?php echo $item['triggered'];?>").<?php if ($enable) echo "disable()"; else echo "hide()";?>;
       }
    });
<?php
        } else if (isset($item['values'])) {
?>
    Event.observe("<?php echo $item['trigger']; ?>","change", function() {
	valueList = [<?php
	$first = true;
	foreach ($item['values'] as $value) {
	    if (!$first)
	       echo ", ";
        echo "\"$value\"";
        $first = false;
	}?>];
    if (valueList.indexOf($F("<?php echo $item['trigger']; ?>"))<?php if (isset($item['reverse'])) echo "=="; else echo "!=";?>-1) {
        $("<?php if (!$enable) echo "form_";?><?php echo $item['triggered'];?>").<?php if ($enable) echo "enable()"; else echo "show()";?>;
    } else {
         $("<?php if (!$enable) echo "form_";?><?php echo $item['triggered'];?>").<?php if ($enable) echo "disable()"; else echo "hide()";?>;
    }
 });
<?php
        } else {
?>
    Event.observe("<?php echo $item['trigger']; ?>","click", function() {
    if (<?php if (isset($item['reverse'])) echo "!" ?>$F("<?php echo $item['trigger']; ?>")) {
        $("<?php if (!$enable) echo "form_";?><?php echo $item['triggered'];?>").<?php if ($enable) echo "enable()"; else echo "show()";?>;
    } else {
         $("<?php if (!$enable) echo "form_";?><?php echo $item['triggered'];?>").<?php if ($enable) echo "disable()"; else echo "hide()";?>;
    }
 });

<?php
        }
    }
?>
    </script>
<?php
    }
?>