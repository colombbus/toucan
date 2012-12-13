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
<li class='submenu_trigger'><?php echo html::anchor("activity/showAll",Kohana::lang('menu.activities'));?>
    <ul class='submenu_actions' style="display:none">
        <li><?php echo html::anchor("activity/create",Kohana::lang('menu.createActivity'));?></li>
    </ul>
</li>
<li class='submenu_trigger'><?php echo html::anchor("evaluation/showAll",Kohana::lang('menu.evaluations'));?>
    <ul class='submenu_actions' style="display:none">
        <li><?php echo html::anchor("evaluation/showAll/1",Kohana::lang('menu.showActiveEvaluations'));?></li>
        <li><?php echo html::anchor("evaluation/showAll/2",Kohana::lang('menu.showPublishedEvaluations'));?></li>
        <li><?php echo html::anchor("evaluation/create",Kohana::lang('menu.createEvaluation'));?></li>
    </ul>
</li>
<li class='submenu_trigger'><?php echo html::anchor("survey/showAll",Kohana::lang('menu.surveys'));?>
    <ul class='submenu_actions' style="display:none">
        <li><?php echo html::anchor("survey/showAll/1",Kohana::lang('menu.showActiveSurveys'));?></li>
        <li><?php echo html::anchor("survey/showAll/2",Kohana::lang('menu.showPublishedSurveys'));?></li>
        <li><?php echo html::anchor("survey/create",Kohana::lang('menu.createSurvey'));?></li>
    </ul>
</li>
<li class='submenu_trigger'><?php echo html::anchor("template/toc",Kohana::lang('menu.templates'));?>
    <ul class='submenu_actions' style="display:none">
        <li><?php echo html::anchor("formTemplate/showAll",Kohana::lang('menu.showFormTemplates'));?></li>
        <li><?php echo html::anchor("interviewTemplate/showAll",Kohana::lang('menu.showInterviewTemplates'));?></li>
        <li><?php echo html::anchor("style/showAll",Kohana::lang('menu.showStyles'));?></li>
    </ul>
</li>
<li class='submenu_trigger'><?php echo html::anchor("user/showAll",Kohana::lang('menu.users'));?>
    <ul class='submenu_actions' style="display:none">
        <li><?php echo html::anchor("group/showAll",Kohana::lang('menu.showGroups'));?></li>
<?php if (isset($adminMenu)) { ?>
        <li><?php echo html::anchor("user/register",Kohana::lang('menu.createUser'));?></li>
<?php } ?>
    </ul>
</li>
