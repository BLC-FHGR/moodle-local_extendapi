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

namespace local_extendapi\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

use external_api;
use external_function_parameters;
use external_single_structure;
use external_multiple_structure;
use external_value;
use invalid_parameter_exception;

class read_all_entries extends external_api {

    public static function execute_parameters() {
        return new external_function_parameters([
            'table' => new external_value(PARAM_TEXT, 'Table Name', VALUE_REQUIRED),
        ]);
    }

    public static function execute($table) {
        global $DB;

        // Validate the table name to prevent SQL injection
        if (!$DB->get_manager()->table_exists($table)) {
            throw new invalid_parameter_exception('Invalid table name');
        }

        $params = self::validate_parameters(self::execute_parameters(), ['table' => $table]);

        $records = $DB->get_records($table);

        // Convert records to desired format
        $results = [];
        foreach ($records as $record) {
            $formatted_record = [];
            foreach ($record as $field => $value) {
                $formatted_record[] = ['field' => $field, 'value' => $value];
            }
            $results[] = $formatted_record;
        }

        return $results;
    }

    public static function execute_returns() {
        return new external_multiple_structure(
            new external_multiple_structure(
                new external_single_structure(
                    array(
                        'field' => new external_value(PARAM_TEXT, 'Field Name'),
                        'value' => new external_value(PARAM_RAW, 'Field Value')
                    )
                )
            )
        );
    }
}
