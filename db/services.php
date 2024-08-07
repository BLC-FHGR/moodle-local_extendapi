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

$functions = [
    'local_extendapi_read_entries' => [
        'classname'   => 'local_extendapi\external\read_entries',
        'description' => 'Searches a specific table based on given criteria.',
        'type'        => 'read',
        'ajax'        => true,
        'services'    => [
            MOODLE_OFFICIAL_MOBILE_SERVICE,
        ]
    ],
    'local_extendapi_read_all_entries' => [
        'classname'   => 'local_extendapi\external\read_all_entries',
        'description' => 'Returns all the data in specific table.',
        'type'        => 'read',
        'ajax'        => true,
        'services'    => [
            MOODLE_OFFICIAL_MOBILE_SERVICE,
        ]
    ],
];

