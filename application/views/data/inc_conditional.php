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
        if (isset($item['triggeredValues'])) {
            $actionTrue =  "$$(\"select#".$item['triggered']." option\").each(function(o){ if (triggeredValues.indexOf(o.readAttribute('value')) != -1) { o.show(); }})";
            $actionFalse =  "$$(\"select#".$item['triggered']." option\").each(function(o){ if (triggeredValues.indexOf(o.readAttribute('value')) != -1) { if (o.selected) resetNeeded = true; o.hide();}})";
        } else {
            $actionTrue =  "$(\"";
            $actionFalse =  "$(\"";
            if (!$enable) {
                $actionTrue .= "form_";
                $actionFalse .= "form_";
            }
            $actionTrue .= $item['triggered']."\").";
            $actionFalse .= $item['triggered']."\").";
            if ($enable) { 
                $actionTrue .= "enable()"; 
                $actionFalse .= "disable()";
            } else {
                $actionTrue .= "show()";
                $actionFalse .= "hide()";
            }
        }
    if (isset($item['value'])||isset($item['values'])) {
?>
        Event.observe("<?php echo $item['trigger']; ?>","change", function() {
<?php
        if (isset($item['triggeredValues'])) {
?>
            triggeredValues = [<?php
            $first = true;
            foreach ($item['triggeredValues'] as $value) {
                if (!$first)
                    echo ", ";
                echo "\"$value\"";
                $first = false;
            }?>];
            resetNeeded = false;
<?php
        }
        if (isset($item['value'])) {
?>
            if ($F("<?php echo $item['trigger']; ?>")<?php if (isset($item['reverse'])) echo "!="; else echo "==";?>"<?php echo $item['value']?>") {
                <?php echo $actionTrue;?>;
            } else {
                <?php echo $actionFalse;?>;
            }
<?php
        } else if (isset($item['values'])) {
?>
            valueList = [<?php
            $first = true;
            foreach ($item['values'] as $value) {
                if (!$first)
                    echo ", ";
                echo "\"$value\"";
                $first = false;
            }?>];
            if (valueList.indexOf($F("<?php echo $item['trigger']; ?>"))<?php if (isset($item['reverse'])) echo "=="; else echo "!=";?>-1) {
                <?php echo $actionTrue;?>;
            } else {
                <?php echo $actionFalse;?>;
            }
<?php
        }
        if (isset($item['triggeredValues'])) {
?>
                if (resetNeeded) {
                    window.alert("yes");
                    list = $("<?php echo $item['triggered']; ?>");
                    list.value = list.down('option').readAttribute('value');
                }
<?php
        }
?>
        });
<?php
    } else {
?>
    Event.observe("<?php echo $item['trigger']; ?>","click", function() {
    if (<?php if (isset($item['reverse'])) echo "!" ?>$F("<?php echo $item['trigger']; ?>")) {
        <?php echo $actionTrue;?>;
    } else {
        <?php echo $actionFalse;?>;
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