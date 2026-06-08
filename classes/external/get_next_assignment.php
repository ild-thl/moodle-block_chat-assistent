<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin test webservice.
 *
 * @package     block_sidekick
 * @category    external
 * @copyright   2026 Your Name <you@example.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_sidekick\external;

use completion_info;
use context_course;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\restricted_context_exception;
use invalid_parameter_exception;

/**
 * Description
 */
class get_next_assignment extends external_api {

    /**
     * Execute the service.
     *
     *
     * @return string true
     * @throws restricted_context_exception
     * @throws invalid_parameter_exception
     */
    public static function execute($courseid): string {
        global $DB, $CFG, $USER;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');

        $params = self::validate_parameters(self::execute_parameters(), ["courseid" => $courseid]);
        $courseid = $params["courseid"];

        // Security checks.
        $context = context_course::instance($courseid);
        $course = get_course($courseid);
        self::validate_context($context);
        require_capability('mod/assign:view', $context);
        // TODO: Add capability/ies needed in the plugin! require_capability('block/disealytics:editlearnerdashboard', $context); .

        $modinfo = get_fast_modinfo($courseid);
        $assigns = $modinfo->get_instances_of('assign');
        $completion = new completion_info($course);

        foreach ($assigns as $assign) {
            // Skip hidden assignments.
            if (!$assign->uservisible) {
                continue;
            }
            // Ignore assignments that don't have completion enabled.
            if (!$completion->is_enabled($assign)) {
                continue;
            }
            $completiondata = $completion->get_data($assign, false, $USER->id);
            // Ignore completed assignments.
            if ($completiondata->completionstate == COMPLETION_COMPLETE) {
                continue;
            }
            // If we are here, we have found a non-completed assignment. Since we step through assignments in course order,
            // this is the next visible, completable, and incomplete assignment.
            return json_encode($assign->id);
        }
        // We found no next assignment.  We would already have returned above.
        return json_encode(false);
    }

    /**
     * Describes the parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(['courseid' => new external_value(PARAM_INT, 'courseid', VALUE_REQUIRED)]);
    }

    /**
     * Describes the return structure of the service
     *
     * @return external_value jsonobj
     */
    public static function execute_returns(): external_value {
        return new external_value(PARAM_RAW, "Module id of the next assignment, or false if none.");
    }
}
