<?php
    ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<title>Toucan Installation (Step 1: Kohana framework)</title>

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
            <div id='header_subtitle'>Step 1: Kohana framework</div>
        </div>
    	<div id='content' class='content' >

            <h1>Environment Tests</h1>

            <p>The following tests have been run to determine if Kohana will work in your environment. If any of the tests have failed, consult the <a href="http://docs.kohanaphp.com/installation">documentation</a> for more information on how to correct the problem.</p>

            <div id="tests">
                <?php $failed = FALSE ?>
                <table cellspacing="0">
                    <tr>
                        <th>PHP Version</th>
                        <?php if (version_compare(PHP_VERSION, '5.2', '>=')): ?>
                        <td class="pass"><?php echo PHP_VERSION ?></td>
                        <?php else: $failed = TRUE ?>
                        <td class="fail">Kohana requires PHP 5.2 or newer, this version is <?php echo PHP_VERSION ?>.</td>
                        <?php endif ?>
                    </tr>
                    <tr>
                        <th>System Directory</th>
                        <?php if (is_dir(SYSPATH) AND is_file(SYSPATH.'core/Bootstrap'.EXT)): ?>
                        <td class="pass"><?php echo SYSPATH ?></td>
                        <?php else: $failed = TRUE ?>
                        <td class="fail">The configured <code>system</code> directory does not exist or does not contain required files.</td>
                        <?php endif ?>
                    </tr>
                    <tr>
                        <th>Application Directory</th>
                        <?php if (is_dir(APPPATH) AND is_file(APPPATH.'config/config'.EXT)): ?>
                        <td class="pass"><?php echo APPPATH ?></td>
                        <?php else: $failed = TRUE ?>
                        <td class="fail">The configured <code>application</code> directory does not exist or does not contain required files.</td>
                        <?php endif ?>
                    </tr>
                    <tr>
                        <th>Modules Directory</th>
                        <?php if (is_dir(MODPATH)): ?>
                        <td class="pass"><?php echo MODPATH ?></td>
                        <?php else: $failed = TRUE ?>
                        <td class="fail">The configured <code>modules</code> directory does not exist or does not contain required files.</td>
                        <?php endif ?>
                    </tr>
                    <tr>
                        <th>PCRE UTF-8</th>
                        <?php if ( !function_exists('preg_match')): $failed = TRUE ?>
                        <td class="fail"><a href="http://php.net/pcre">PCRE</a> support is missing.</td>
                        <?php elseif ( ! @preg_match('/^.$/u', 'ñ')): $failed = TRUE ?>
                        <td class="fail"><a href="http://php.net/pcre">PCRE</a> has not been compiled with UTF-8 support.</td>
                        <?php elseif ( ! @preg_match('/^\pL$/u', 'ñ')): $failed = TRUE ?>
                        <td class="fail"><a href="http://php.net/pcre">PCRE</a> has not been compiled with Unicode property support.</td>
                        <?php else: ?>
                        <td class="pass">Pass</td>
                        <?php endif ?>
                    </tr>
                    <tr>
                        <th>Reflection Enabled</th>
                        <?php if (class_exists('ReflectionClass')): ?>
                        <td class="pass">Pass</td>
                        <?php else: $failed = TRUE ?>
                        <td class="fail">PHP <a href="http://www.php.net/reflection">reflection</a> is either not loaded or not compiled in.</td>
                        <?php endif ?>
                    </tr>
                    <tr>
                        <th>Filters Enabled</th>
                        <?php if (function_exists('filter_list')): ?>
                        <td class="pass">Pass</td>
                        <?php else: $failed = TRUE ?>
                        <td class="fail">The <a href="http://www.php.net/filter">filter</a> extension is either not loaded or not compiled in.</td>
                        <?php endif ?>
                    </tr>
                    <tr>
                        <th>Iconv Extension Loaded</th>
                        <?php if (extension_loaded('iconv')): ?>
                        <td class="pass">Pass</td>
                        <?php else: $failed = TRUE ?>
                        <td class="fail">The <a href="http://php.net/iconv">iconv</a> extension is not loaded.</td>
                        <?php endif ?>
                    </tr>

                    <tr>
                        <th>SPL Enabled</th>
                        <?php if (function_exists('spl_autoload_register')): ?>
                        <td class="pass">Pass</td>
                        <?php else: $failed = TRUE ?>
                        <td class="fail"><a href="http://php.net/spl">SPL</a> is not enabled.</td>
                        <?php endif ?>
                    </tr>

                    <?php if (extension_loaded('mbstring')): ?>
                    <tr>
                        <th>Mbstring Not Overloaded</th>
                        <?php if (ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING): $failed = TRUE ?>
                        <td class="fail">The <a href="http://php.net/mbstring">mbstring</a> extension is overloading PHP's native string functions.</td>
                        <?php else: ?>
                        <td class="pass">Pass</td>
                    </tr>
                    <?php endif ?>
                    <?php else: // check for utf8_[en|de]code when mbstring is not available ?>
                    <tr>
                        <th>XML support</th>
                        <?php if ( ! function_exists('utf8_encode')): $failed = TRUE ?>
                        <td class="fail">PHP is compiled without <a href="http://php.net/xml">XML</a> support, thus lacking support for <code>utf8_encode()</code>/<code>utf8_decode()</code>.</td>
                        <?php else: ?>
                        <td class="pass">Pass</td>
                        <?php endif ?>
                    </tr>
                    <?php endif ?>
                    <tr>
                        <th>URI Determination</th>
                        <?php if (isset($_SERVER['REQUEST_URI']) OR isset($_SERVER['PHP_SELF'])): ?>
                        <td class="pass">Pass</td>
                        <?php else: $failed = TRUE ?>
                        <td class="fail">Neither <code>$_SERVER['REQUEST_URI']</code> or <code>$_SERVER['PHP_SELF']</code> is available.</td>
                        <?php endif ?>
                    </tr>
                </table>

                <div id="results">
                    <?php if ($failed === TRUE): ?>
                    <p class="fail">Kohana may not work correctly with your environment.</p>
                    <?php else: ?>
                    <p class="pass">Your environment passed all requirements. You may remove or rename file <code>install<?php echo EXT ?></code>.</p>
                    <?php endif ?>
                </div>
            </div>
		</div>
    </div>
</body>
</html>
<?php
    if ($failed === TRUE) {
        ob_end_flush();
    } else {
        // first tests have passed: cancel the output and move on to application tests
        ob_end_clean();
        define('INSTALL_IN_PROGRESS', true);
        // Initialize Kohana
        require SYSPATH.'core/Bootstrap'.EXT;
    }
?>