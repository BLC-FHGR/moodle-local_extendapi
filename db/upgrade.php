<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Version metadata for the plugintype_pluginname plugin.
 *
 * @package   local_extendapi
 * @copyright 2024, Julien Rädler <julien.raedler@fhgr.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_extendapi_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    $newversion = 2024072900;

    if ($oldversion < $newversion) {

        // Definiere die externe Funktion, die hinzugefügt oder aktualisiert werden soll.
        $external_function = new stdClass();
        $external_function->name = 'local_extendapi_read_activities';
        $external_function->classname = 'local_extendapi\\external\\read_activities';
        $external_function->methodname = 'execute';
        $external_function->classpath = 'local/extendapi/classes/external/read_activities.php';
        $external_function->component = 'local_extendapi';
        $external_function->capabilities = '';
        $external_function->services = 'moodle_mobile_app';

        // Überprüfen, ob der Eintrag bereits existiert
        if ($existing_function = $DB->get_record('external_functions', array('name' => 'local_extendapi_read_activities'))) {
            // Falls der Eintrag existiert, aktualisiere ihn
            $external_function->id = $existing_function->id;
            $DB->update_record('external_functions', $external_function);
        } else {
            // Falls der Eintrag nicht existiert, füge ihn hinzu
            $DB->insert_record('external_functions', $external_function);
        }

        // Moodle Versionsnummer aktualisieren.
        upgrade_plugin_savepoint(true, $newversion, 'local', 'extendapi');
    }

    return true;
}
