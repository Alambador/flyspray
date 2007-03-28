<?php
// +----------------------------------------------------------------------
// | PHP Source
// +----------------------------------------------------------------------
// | Copyright (C) 2006  by Cristian Rodriguez R <judas.iscariote@flyspray.org>
// | Copyright (C) 2007  by Florian  Florian Schmitz <floele@flyspray.org>
// +----------------------------------------------------------------------
// |
// | Copyright: See COPYING file that comes with this distribution
// +----------------------------------------------------------------------
//

@set_time_limit(0);
ini_set('memory_limit', '32M');

// define basic stuff first.
define('IN_FS', 1);
define('IN_UPGRADER', 1);
define('BASEDIR', dirname(__FILE__));
define('OBJECTS_PATH', BASEDIR . '/../includes');
define('TEMPLATE_FOLDER', BASEDIR . '/../setup/templates/');

$borked = str_replace( 'a', 'b', array( -1 => -1 ) );

if(!isset($borked[-1])) {
    die("Flyspray cannot run here, sorry :-( \n PHP 4.4.x/5.0.x is buggy on your 64-bit system; you must upgrade to PHP 5.1.x\n" .
        "or higher. ABORTING. (http://bugs.php.net/bug.php?id=34879 for details)\n");
}

require_once OBJECTS_PATH . '/fix.inc.php';
require_once OBJECTS_PATH . '/class.gpc.php';
require_once OBJECTS_PATH . '/class.database.php';
require_once OBJECTS_PATH . '/class.flyspray.php';
@require_once OBJECTS_PATH . '/class.tpl.php';

define('CONFIG_PATH', Flyspray::get_config_path(BASEDIR . '/../'));
$conf  = @parse_ini_file(CONFIG_PATH, true) or die('Cannot open config file at ' . CONFIG_PATH);

// Initialise DB
require_once BASEDIR . '/../adodb/adodb.inc.php';
require_once BASEDIR . '/../adodb/adodb-xmlschema03.inc.php';

$db = NewDatabase($conf['database']);

// ---------------------------------------------------------------------
// Application Web locations
// ---------------------------------------------------------------------
$fs = new Flyspray();

define('APPLICATION_SETUP_INDEX', Flyspray::absoluteURI());
define('UPGRADE_VERSION', Flyspray::base_version($fs->version));

// Get installed version
$installed_version = $db->GetOne('SELECT pref_value FROM {prefs} WHERE pref_name = ?', array('fs_ver'));

$page = new Tpl;
$page->assign('title', 'Upgrade ');
$page->assign('short_version', UPGRADE_VERSION);

// ---------------------------------------------------------------------
// Now the hard work
// ---------------------------------------------------------------------

// Find out which upgrades need to be run
$folders = glob_compat(BASEDIR . '/upgrade/[0-9]*');
usort($folders, 'version_compare'); // start with lowest version

$upgrade_available = false;
if (Post::val('upgrade')) {
    foreach ($folders as $folder) {
        if (version_compare($installed_version, $folder, '<=')) {
            execute_upgrade_file($folder, $installed_version);
            $installed_version = $folder;
        }
    }
    // we should be done at this point
    $db->Query('UPDATE {prefs} SET pref_value = ? WHERE pref_name = ?', array($fs->version, 'fs_ver'));
    $installed_version = $fs->version;
}
foreach ($folders as $folder) {
    if (version_compare($installed_version, $folder, '<')) {
        $upgrade_available = true;
    }
    // or dev version
    if ($folder == Flyspray::base_version($installed_version)
        && version_compare($installed_version, $folder, '<=')) {
        $upgrade_available = true;
    }
}

function execute_upgrade_file($folder, $installed_version)
{
    global $db, $page, $conf;
    // At first the config file
    $upgrade_path = BASEDIR . '/upgrade/' . $folder;
    new ConfUpdater(CONFIG_PATH, $upgrade_path);

    $upgrade_info = parse_ini_file($upgrade_path . '/upgrade.info', true);
    $type = 'defaultupgrade';

    // global prefs update
    if (isset($upgrade_info['fsprefs'])) {
        $existing = $db->GetCol('SELECT pref_name FROM {prefs}');
        // Add what is missing
        foreach ($upgrade_info['fsprefs'] as $name => $value) {
            if (!in_array($name, $existing)) {
                $db->Execute('INSERT INTO {prefs} (pref_name, pref_value) VALUES (?, ?)', array($name, $value));
            }
        }
        // Delete what is too much
        foreach ($existing as $name) {
            if (!isset($upgrade_info['fsprefs'][$name])) {
                $db->Execute('DELETE FROM {prefs} WHERE pref_name = ?', array($name));
            }
        }
    }

    // Now a mix of XML schema files and PHP upgrade scripts
    if (!isset($upgrade_info[$type])) {
        die('#1 Bad upgrade.info file.');
    }

    // files which are already done
    $done = $db->GetOne('SELECT pref_value FROM {prefs} WHERE pref_name = ?', array('upgrader_done'));
    $done = ($done) ? unserialize($done) : array();

    ksort($upgrade_info[$type]);
    foreach ($upgrade_info[$type] as $file) {
        // skip all files which have been executed already
        $hash = md5_file($upgrade_path . '/' . $file);
        if (isset($done[$file]) && $done[$file] == $hash) {
            continue;
        }

        if (substr($file, -4) == '.php') {
            require_once $upgrade_path . '/' . $file;
            $done[$file] = $hash;
        }

        if (substr($file, -4) == '.xml') {
            $schema = new adoSchema($db);
            $schema->SetPrefix($conf['database']['dbprefix']);
            $schema->ParseSchemaFile($upgrade_path . '/' . $file);
            if ($schema->ExecuteSchema()) {
                $done[$file] = $hash;
            }
        }
    }

    $db->Execute('UPDATE {prefs} SET pref_value = ? WHERE pref_name = ?', array($folder, 'fs_ver'));
    $db->Execute('UPDATE {prefs} SET pref_value = ? WHERE pref_name = ?', array(serialize($done), 'upgrader_done'));
    $page->assign('done', true);
}

class ConfUpdater
{
    var $old_config = array();
    var $new_config = array();

    /**
     * Reads the existing config file and updates it
     * @param string $location
     * @access public
     * @return bool
     */
    function ConfUpdater($location, $upgrade_path)
    {
        if (!is_writable($location)) {
            return false;
        }

        $this->old_config = parse_ini_file($location, true);
        $this->new_config = parse_ini_file($upgrade_path . '/flyspray.conf.php', true);
        // Now we overwrite all values of the *default* file if there is one in the existing config
        array_walk($this->new_config, array($this, '_merge_configs'));

        $this->_write_config($location);

    }

    /**
     * Callback function, merges config values
     * @param array $settings
     * @access private
     * @return array
     */
    function _merge_configs(&$settings, $group)
    {
        foreach ($settings as $key => $value) {
            if (isset($this->old_config[$group][$key])) {
                $settings[$key] = $this->old_config[$group][$key];
            }
            // Upgrade to MySQLi if possible
            if ($key == 'dbtype' && strtolower($settings[$key]) == 'mysql' && function_exists('mysqli_connect')) {
                //mysqli is broken on 64bit systems in versions < 5.1 do not use it, tested, does not work.
                if (php_uname('m') == 'x86_64' && version_compare(phpversion(), '5.1.0', '<')) {
                    continue;
                }
                $settings[$key] = 'mysqli';
            }
        }
    }

    /**
     * Writes the new config file to a given $location
     * @param string $location
     * @access private
     */
    function _write_config($location)
    {
        $new_config = "; <?php die( 'Do not access this page directly.' ); ?>\n\n";
        foreach ($this->new_config as $group => $settings) {
            $new_config .= "[{$group}]\n";
            foreach ($settings as $key => $value) {
                $new_config .= $key . '="' . str_replace('"', '\"', $value) . '"' . "\n";
            }
            $new_config .= "\n";
        }

        $fp = fopen($location, 'wb');
        fwrite($fp, $new_config);
        fclose($fp);
    }
}

$checks = $todo = array();
$checks['version_compare'] = version_compare($installed_version, UPGRADE_VERSION) === -1;
$checks['config_writable'] = is_writable(CONFIG_PATH);
$checks['db_connect'] = (bool) $db;
$checks['installed_version'] = version_compare($installed_version, '0.9.5') === 1;
$checks['upgrade_required'] = $upgrade_available;
$todo['config_writable'] = 'Please make sure that the file at ' . CONFIG_PATH . ' is writable.';
$todo['db_connect'] = 'Connection to the database could not be established. Check your config.';
$todo['version_compare'] = 'No newer version than yours can be installed with this upgrader.';
$todo['installed_version'] = 'An upgrade from Flyspray versions lower than 0.9.6 is not possible.
                              You will have to upgrade manually to at least 0.9.6, the scripts which do that are included in all Flyspray releases <= 0.9.8.';
$todo['upgrade_required'] = 'A database upgrade is not required. Flyspray should work properly already.';

$upgrade_possible = true;
foreach ($checks as $check => $result) {
    if ($result !== true) {
        $upgrade_possible = false;
        $page->assign('todo', $todo[$check]);
        break;
    }
}

if (isset($upgrade_info['options'])) {
    // piece of HTML which adds user input, quick and dirty
    $page->assign('upgrade_options', implode('', $upgrade_info['options']));
}

$page->assign('index', APPLICATION_SETUP_INDEX);
$page->uses('checks', 'fs', 'upgrade_possible');
$page->assign('installed_version', $installed_version);

$page->display('upgrade.tpl');

?>
