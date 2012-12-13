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
/**
 * Extended pagination style
 *
 * @preview  « Previous | Page 2 of 11 | Showing items 6-10 of 52 | Next »
 */
?>
<?php if ($previous_page): ?>
	<a href="javascript:updateList('<?php echo str_replace('{page}', $previous_page, $url) ?>')">&laquo;&nbsp;<?php echo Kohana::lang('pagination.previous') ?></a> |
<?php else: ?>
	<span class='deactivated'>&laquo;&nbsp;<?php echo Kohana::lang('pagination.previous') ?> |</span>
<?php endif ?>
<?php echo Kohana::lang('pagination.page') ?> <?php echo $current_page ?> <?php echo Kohana::lang('pagination.of') ?> <?php echo $total_pages ?>

| <?php echo Kohana::lang('pagination.items') ?> <?php echo $current_first_item ?>&ndash;<?php echo $current_last_item ?> <?php echo Kohana::lang('pagination.of') ?> <?php echo $total_items ?>
<?php if ($next_page): ?>
| <a href="javascript:updateList('<?php echo str_replace('{page}', $next_page, $url) ?>')"><?php echo Kohana::lang('pagination.next') ?>&nbsp;&raquo;</a>
<?php else: ?>
	<span class='deactivated'>| <?php echo Kohana::lang('pagination.next') ?>&nbsp;&raquo;</span>
<?php endif ?>
