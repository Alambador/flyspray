<?php

/*
 * This file is meant to add every hack that is needed to fix default PHP
 * behaviours, and to ensure that our PHP env will be able to run flyspray
 * correctly.
 *
 */
ini_set('display_errors', 1);

// html errors will mess the layout
ini_set('html_errors', 0);

error_reporting(E_ALL & ~E_DEPRECATED);

// our default charset

ini_set('default_charset','utf-8');

// This to stop PHP being retarded and using the '&' char for session id delimiters
ini_set('arg_separator.output','&amp;');

// MySQLi driver is _useless_ if zend.ze1_compatibility_mode is enabled
// in fact you should never use this setting,the damn thing does not work.

ini_set('zend.ze1_compatibility_mode',0);


//we don't want magic_quotes_runtime ..

ini_set('magic_quotes_runtime',0);

//this one too
ini_set('magic_quotes_sybase',0);

// no transparent session id improperly configured servers

@ini_set('session.use_trans_sid', 0); // might cause error in setup

//see http://php.net/manual/en/ref.session.php#ini.session.use-only-cookies
ini_set('session.use_only_cookies',1);

//no session auto start
ini_set('session.auto_start',0);

/*this stops most cookie attacks via XSS at the interpreter level
* see http://msdn.microsoft.com/workshop/author/dhtml/httponly_cookies.asp
* supported by IE 6 SP1, Safari, Konqueror, Opera, silently ignored by others
* ( sadly, including firefox) available since PHP 5.2.0
 */

ini_set('session.cookie_httponly',1);


ini_set('include_path', join( PATH_SEPARATOR, array(
  dirname(__FILE__) ,
  dirname(__FILE__) . '/external' ,
  dirname(__FILE__) . '/external/swift-mailer',
  dirname(__FILE__) . '/external/compat',
  ini_get('include_path'))));

// we live is register_globals Off world forever..
//This code was written By Stefan Esser from the hardened PHP project (sesser@php.net)
// it's now part of the PHP manual

function unregister_GLOBALS()
{
   if (!ini_get('register_globals')) {
       return;
   }

   // Might want to change this perhaps to a nicer error
   if (isset($_REQUEST['GLOBALS']) || isset($_FILES['GLOBALS'])) {
       die('GLOBALS overwrite attempt detected');
   }

   // Variables that shouldn't be unset
   $noUnset = array('GLOBALS',  '_GET',
                     '_POST',    '_COOKIE',
                     '_REQUEST', '_SERVER',
                     '_ENV',    '_FILES');

   $input = array_merge($_GET,    $_POST,
                         $_COOKIE, $_SERVER,
                         $_ENV,    $_FILES,
                         isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());

   foreach ($input as $k => $v) {
       if (!in_array($k, $noUnset) && isset($GLOBALS[$k])) {

           unset($GLOBALS[$k]);
           /* no, this is not a bug, we use double unset() .. it is to circunvent
           /* this PHP critical vulnerability
            * http://www.hardened-php.net/hphp/zend_hash_del_key_or_index_vulnerability.html
            * this is intended to minimize the catastrophic effects that has on systems with
            * register_globals on.. users with register_globals off are still vulnerable but
            * afaik,there is nothing we can do for them.
            */
           unset($GLOBALS[$k]);
       }
   }
}

unregister_GLOBALS();


/*unless we want to use this in the future, get rid of the
* the PHP >= 5.2 , input filter extension, if not, it
* will mess with user input if sysadmin or webmaster use a filter different
* than the default.
* This is based on work by Tobias Schlitt <toby@php.net> available under
* the BSD license, but has been slightly  modified for Flyspray.
*/

if (PHP_VERSION >= 5.2 && extension_loaded('filter') && filter_id(ini_get('filter.default')) !== FILTER_UNSAFE_RAW) {

    if(count($_GET)) {
        foreach ($_GET as $key => $value) {
            $_GET[$key] = filter_input(INPUT_GET, $key, FILTER_UNSAFE_RAW);
        }
    }
    if(count($_POST)) {
        foreach ($_POST as $key => $value) {
            $_POST[$key] = filter_input(INPUT_POST, $key, FILTER_UNSAFE_RAW);
        }
    }
    if(count($_COOKIE)) {
        foreach ($_COOKIE as $key => $value) {
            $_COOKIE[$key] = filter_input(INPUT_COOKIE, $key, FILTER_UNSAFE_RAW);
        }
    }
    if(isset($_SESSION) && is_array($_SESSION) && count($_SESSION)) {
        foreach ($_SESSION as $key => $value) {
            $_SESSION[$key] = filter_input(INPUT_SESSION, $key, FILTER_UNSAFE_RAW);
        }
    }

}

// This is for retarded Windows servers not having REQUEST_URI

if (!isset($_SERVER['REQUEST_URI']))
{
    if (isset($_SERVER['SCRIPT_NAME'])) {
        $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
    }
    else {
        // this is tained now.
        $_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'];
    }

    if (isset($_SERVER['QUERY_STRING'])) {
        $_SERVER['REQUEST_URI'] .=  '?'.$_SERVER['QUERY_STRING'];
    }
}

if (!isset($_SERVER['QUERY_STRING']))
{
    $_SERVER['QUERY_STRING'] = '';
}

/* we also don't want magic_quotes_gpc at all
 * this code was written by Ilia Alshanetsky <iilia@php.net>
 * is licensed under the BSD.
 */

function undo_magic_quotes(&$var)
{
    if (is_array($var)) {
        foreach ($var as $k => $v) {
            if (is_array($v)) {
                array_walk($var[$k], 'undo_magic_quotes');
            } else {
                $var[$k] = stripslashes($v);
            }
        }
    } else {
        $var = stripslashes($var);
    }
}

if (ini_get('magic_quotes_gpc')) {
    if (count($_REQUEST)) {
        array_walk($_REQUEST, 'undo_magic_quotes');
    }

    if (count($_GET)) {
        array_walk($_GET,     'undo_magic_quotes');
    }

    if (count($_POST)) {
        array_walk($_POST,    'undo_magic_quotes');
    }

    if (count($_COOKIE)) {
        array_walk($_COOKIE, 'undo_magic_quotes');
    }

    if (count($_FILES) && strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        array_walk($_FILES,   'undo_magic_quotes');
    }
}

/**
 * Replace glob() since this function is apparently
 * disabled for no apparent reason ("security") on some systems
 *
 * @see glob()
 */

function glob_compat($pattern, $flags = 0) {

    if(in_array('glob', explode(',', ini_get('disable_functions'))) || !function_exists('glob')) {

        include 'glob.php';

        return php_compat_glob($pattern, $flags);
    }
        return glob($pattern, $flags);
}

// now for all those borked PHP installations...
if (!function_exists('hash_hmac')) {

    function hash_hmac($algo, $data, $key, $raw_output = false) {

        if(function_exists('mhash') && $algo == 'md5') {
            return $raw_output ? mhash(MHASH_MD5, $data, $key) : bin2hex(mhash(MHASH_MD5, $data, $key));
        }

        include_once 'HMAC.php';

        $hashobj = new Crypt_HMAC($key, $algo);

        return $raw_output ? pack('H*', $hashobj->hash($data)) : $hashobj->hash($data);
    }
}

// for reasons outside flsypray, the PHP core may throw Exceptions in PHP5
// for a good example see this article
// http://ilia.ws/archives/107-Another-unserialize-abuse.html

function flyspray_exception_handler($exception) {
    die("Completely unexpected exception: " .
        htmlspecialchars($exception->getMessage(),ENT_QUOTES, 'utf-8')  . "<br/>" .
      "This should <strong> never </strong> happend, please inform Flyspray Developers");

}
set_exception_handler('flyspray_exception_handler');

// We don't need session IDs in URLs
output_reset_rewrite_vars();
?>