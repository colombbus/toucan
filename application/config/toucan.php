<?php defined('SYSPATH') OR die('No direct access allowed.');
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

    $config['registration_auto'] = true;
    $config['registration_email_confirmation'] = true;
    $config['new_user_admin_only'] = true;
    $config['user_per_page'] = 20;
    $config['group_per_page'] = 20;
    $config['formTemplate_per_page'] = 20;
    $config['interviewTemplate_per_page'] = 20;
    $config['evaluation_per_page'] = 20;
    $config['formSession_per_page'] = 20;
    $config['interviewSession_per_page'] = 20;
    $config['file_per_page'] = 20;
    $config['styleFile_per_page'] = 20;
    $config['formCopy_per_page'] = 20;
    $config['interviewCopy_per_page'] = 20;
    $config['style_per_page'] = 20;
    $config['survey_per_page'] = 20;
    $config['surveyCopy_per_page'] = 20;
    $config['avatar_directory']='media/public/avatar';
    $config['logo_directory']='media/public/avatar';
    $config['files_directory']='media/public';
    $config['images_directory']='media/images';
    $config['templates_directory']='templates';
    $config['css_directory']='media/style';
    $config['js_directory']='media/js';
    $config['graphic_width']=600;
    $config['graphic_height']=200;
    $config['graphic_legend_line_height']=24;
    $config['graphic_max_chars'] = 40;
    $config['graphic_pie_size'] = 60;
    $config['separator_color']='FFE8DD';
    $config['sub_separator_color']='FFF0C6';
    $config['public_file_mode']=0664;
    $config['public_directory_mode']=0774;
?>