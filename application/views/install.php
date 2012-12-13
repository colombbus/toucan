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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<title>Toucan Installation (Step 2: Application configuration)</title>

<style type="text/css">
    body                {background-color:#FFFFFF;;color:#385d8a;font-size:9pt;font-family:verdana,"lucida sans unicode",arial, sans-serif;text-align:center;}
    #container          {width:850px;margin-left:auto;margin-right:auto;text-align:left;overflow:hidden;border:solid 1px #E0E0E0;}
    #footer             {clear:both;color:#558ED5;padding-top:30px;}
    #footer_bottom      {background-image:url('images/content_bottom.png');height:14px;overflow:hidden;}
    #footer_left        {float:left;background-image:url('images/content_bottom_left.png');height: 14px;width:14px;padding: 0px;}
    #footer_right       {float:right;background-image:url('images/content_bottom_right.png');height: 14px;width:14px;padding: 0px;}
    #header             {text-align:center;color:#385D8A;height:120px;overflow:hidden;}
    #header_title       {font-size:3em;font-weight:bold;margin-left:auto;margin-right:auto;}
    #header_subtitle    {font-size:1.8em;font-weight:normal;margin-left:auto;margin-right:auto;color:#119911}
    #content            {margin-left:auto;margin-top:20px;margin-right:auto;width:750px;}
    #tests table        { border-collapse: collapse; width: 100%; }
    #tests table th,
    #tests table td     { padding: 0.2em 0.4em; text-align: left; vertical-align: top; }
    #tests table th     { width: 12em; font-weight: normal; font-size: 1.2em; }
    #tests table tr:nth-child(odd) { background: #eee; }
    #tests table td.pass { color: #191; }
    #tests table td.fail { color: #911; }
    #tests #results     { color: #fff; }
    #tests #results p   { padding: 0.8em 0.4em; }
    #tests #results p.pass { background: #191; }
    #tests #results p.fail { background: #911; }
</style>

</head>
<body>
  	<div id='container'>
        <div id='header' class='header'>
            <div id='header_title'>Toucan installation</div>
            <div id='header_subtitle'>Step 2: Application configuration</div>
        </div>
    	<div id='content' class='content' >

            <h1>Configuration Tests</h1>

            <p>The following tests have been run to determine if Toucan application is correctly configured. If any of the tests have failed, consult the <a href="http://toucan.colombbus.org/">documentation</a> for more information on how to correct the problem.</p>

            <div id="tests">
                <table cellspacing="0">
                    <?php
                        foreach ($tests as $test) {
                            echo "<tr>";
                            echo "<th>".$test['title']."</th>";
                            if ($test['pass']) {
                                echo "<td class='pass'>".$test['result']."</td>";
                            } else {
                                echo "<td class='fail'>".$test['result']."</td>";
                            }
                            echo "</tr>";
                        }
                    ?>
                </table>
                <div id="results">
                    <?php if ($failed === TRUE): ?>
                    <p class="fail">Toucan may not work correctly with your environment.</p>
                    <?php else: ?>
                    <p class="pass">Your environment passed all requirements. Remove or rename the <code>install<?php echo EXT ?></code> file now.</p>
                    <?php endif ?>
                </div>
            </div>
		</div>
    </div>
</body>
</html>