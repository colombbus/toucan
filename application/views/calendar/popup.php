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
<?php defined('SYSPATH') OR die('No direct access allowed.');
// Get the day names
$days = Calendar::days(2);

// Previous and next month timestamps
$nextMonth = mktime(0, 0, 0, $month + 1, 1, $year);
$nextYear = mktime(0, 0, 0, $month, 1, $year+1);
$prevMonth = mktime(0, 0, 0, $month - 1, 1, $year);
$prevYear = mktime(0, 0, 0, $month, 1, $year-1);

// Import the GET query array locally and remove the day
$qs = $_GET;
unset($qs['day']);

// Previous and next month query URIs
$prevMonth ="new Ajax.Updater('popup_calendar_$index', '$base_url/".date('n', $prevMonth)."/".date('Y', $prevMonth)."', { method: 'get' });";
$prevYear ="new Ajax.Updater('popup_calendar_$index', '$base_url/".date('n', $prevYear)."/".date('Y', $prevYear)."', { method: 'get' });";
$nextMonth ="new Ajax.Updater('popup_calendar_$index', '$base_url/".date('n', $nextMonth)."/".date('Y', $nextMonth)."', { method: 'get' });";
$nextYear ="new Ajax.Updater('popup_calendar_$index', '$base_url/".date('n', $nextYear)."/".date('Y', $nextYear)."', { method: 'get' });";


?>
<table class="calendar_popup" style="font-size:10pt;width:100%;">
<tr class="controls">
<td style="cursor:pointer" class="prev calendar_cell calendar_header" onclick="<?php echo $prevYear; ?>">&laquo;</td>
<td style="cursor:pointer" class="prev calendar_cell calendar_header" onclick="<?php echo $prevMonth; ?>">&lt;</td>
<td style="text-align:center" colspan="3" class="calendar_header"></td>
<td style="cursor:pointer" class="next calendar_cell calendar_header" onclick="<?php echo $nextMonth; ?>">&gt;</td>
<td style="cursor:pointer" class="next calendar_cell calendar_header" onclick="<?php echo $nextYear; ?>">&raquo;</td>
</tr>
<tr>
<td class="calendar_title" style="text-align:center" colspan="7"><?php 
setlocale(LC_TIME, "en_US");
echo Kohana::lang("calendar.".strtolower(strftime('%B', mktime(0, 0, 0, $month, 1, $year))))." ".$year; 
setLocale(LC_TIME, NULL);
?></td>
</tr>
<tr>
<?php foreach ($days as $day): ?>
<td class="calendar_cell calendar_day"><?php echo $day ?></td>
<?php endforeach ?>
</tr>
<?php foreach ($weeks as $week): ?>
<tr>
<?php foreach ($week as $day): ?>
<td class="calendar_cell">
<?php if ($day[1]) {
?>
    <span class="day" style="cursor:pointer" onClick="$('date_<?php echo $index; ?>').value='<?php echo date(Kohana::lang("calendar.format"),mktime(0, 0, 0, $month, $day[0], $year)); ?>';$('popup_calendar_<?php echo $index; ?>').hide();"> <?php echo $day[0] ?></span></td>
<?php } else { ?>
    <span class="day_off"> </span></td>
<?php } ?>
<?php endforeach ?>
</tr>
<?php endforeach ?>
</table>