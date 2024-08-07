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

class read_entries extends external_api {

    public static function execute_parameters() {
        return new external_function_parameters([
            'table' => new external_value(PARAM_TEXT, 'table name', VALUE_REQUIRED),
            'criteria' => new external_multiple_structure(
                new external_single_structure([
                    'field' => new external_value(PARAM_TEXT, 'Field Name', VALUE_DEFAULT, null),
                    'value' => new external_value(PARAM_RAW, 'Field Value', VALUE_DEFAULT, null),
                ], 'Optional filters for the log entries', VALUE_DEFAULT, [])
            )
        ]);
    }

    public static function execute($table, $criteria) {
        global $DB;

        // Validate the table name to prevent SQL injection
        if (!$DB->get_manager()->table_exists($table)) {
            throw new invalid_parameter_exception('Invalid table name');
        }

        // Validate columns
        $columns = $DB->get_columns($table);
        if (!$columns) {
            throw new invalid_parameter_exception('Invalid table columns');
        }

        // Validate the criteria
        $valid_columns = array_keys($columns);
        foreach ($criteria as $criterion) {
            if (!in_array($criterion['field'], $valid_columns)) {
                throw new invalid_parameter_exception('Invalid field name: ' . $criterion['field']);
            }
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $criterion['field'])) {
                throw new invalid_parameter_exception('Invalid field name: ' . $criterion['field']);
            }
        }

        $params = self::validate_parameters(self::execute_parameters(), ['table' => $table, 'criteria' => $criteria]);

        $conditions = array();
        $params = array();

        foreach ($criteria as $criterion) {
            $conditions[] = $criterion['field'] . " = :" . $criterion['field'];
            $params[$criterion['field']] = $criterion['value'];
        }

        $where = implode(' AND ', $conditions);
        $sql = "SELECT * FROM {" . $table . "} WHERE " . $where;

        $records = $DB->get_records_sql($sql, $params);

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
