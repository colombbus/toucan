<?php
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

class Errors {
 public function error404(){
     Session::instance()->keep_flash();
     header('HTTP/1.1 404 File Not Found');
     self::show404(Kohana::lang('error.404'), Kohana::lang('error.404'));
     /*$view = new View("error_template");

     $view->title = Kohana::lang('error.404');
     $view->content = new View("error");
     $view->content->error = Kohana::lang('error.404');
     $view->render(TRUE);*/
     // Run the shutdown even to ensure a clean exit
     if ( ! Event::has_run('system.shutdown')) {
        Event::run('system.shutdown');
     }
     // Turn off error reporting
	 error_reporting(0);
     exit;
 }

 public static function show404($title,$content) {
     echo "<html xmlns='http://www.w3.org/1999/xhtml' lang='en' xml:lang='en'>
            <head>
            <title>$title</title>
            <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>";
     echo html::stylesheet(array("style/toucan.css"),array("all"),FALSE);
     echo "</head>
            <body>
            <div id='container'>
                <div id='header' class='header'>
                    <div id='header_top'>
                        <div id='header_left'></div>
                        <div id='header_right'></div>
                    </div>
                <div id='header_logo'>";
    echo html::anchor('',html::image(array("src"=>"images/toucan.png")));
    echo "      </div>
                <div id='header_language'>";
    echo html::image(array("src"=>"images/graphes.png"));
    echo "          <br/>
                </div>
                <div id='header_center'>
                    <div id='header_title'><?php echo Kohana::lang('main.title'); ?></div>
                    <div id='header_subtitle'><?php echo Kohana::lang('main.subtitle'); ?></div>
                </div>
                <div class='float_end'></div>
            </div>
            <div id='content_public'><div id='error'>$content</div>";
    echo "<div id='actions'><div id='actions_left'>";
    echo form::button(array ('type'=>'button'), Kohana::lang('button.back'),"onClick='history.back()' class='toucan_button'");
    echo "</div><div class='float_end'></div></div>";
    echo "  </div>
            <div id='footer'>
                <div id='footer_bottom'>
                    <div id='footer_left'></div>
                    <div id='footer_right'></div>
                    <div class='end_float'></div>
                </div>
            </div>
        </div></body></html>";
 }
}

Event::clear('system.404', array('Kohana', 'show_404'));
Event::add('system.404', array('Errors', 'error404'));
?>