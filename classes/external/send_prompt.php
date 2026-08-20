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

use context_course;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_value;

/**
 *
 */
class send_prompt extends external_api {

    /**
     * Execute the service.
     *
     *
     * @return array genrated messages
     */
    public static function execute($courseid, $prompt, $intents): array {
        $params = self::validate_parameters(self::execute_parameters(),
                ["courseid" => $courseid, "prompt" => $prompt, "intents" => $intents]);
        foreach ($params as $name => $value) {
            $$name = $value;
        }
        $courseid = $params["courseid"];
        $prompt = $params["prompt"];
        $intents = $params["intents"];

        // Security checks.
        $context = context_course::instance($courseid);
        self::validate_context($context);
        // TODO: Add capability/ies needed in the plugin! require_capability('block/disealytics:editlearnerdashboard', $context); .
        global $USER;
        $action = new \core_ai\aiactions\generate_text(
                contextid: $context->id,
                userid: $USER->id,
                prompttext: $prompt,
        );
        $manager = \core\di::get(\core_ai\manager::class);
        $response = $manager->process_action($action);
        if (!$response->get_success()) {
            return [];
        }
        $responsecontent = (string) ($response->get_response_data()['generatedcontent'] ?? '');
        if (trim($responsecontent) === '') {
            return [];
        }
        return [$responsecontent];
    }

    /**
     * Describes the parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
                'courseid' => new external_value(PARAM_INT, 'courseid', VALUE_REQUIRED),
                'prompt' => new external_value(PARAM_TEXT, 'mode', VALUE_REQUIRED),
                'intents' => new external_multiple_structure(
                        new external_value(
                                PARAM_TEXT,
                                'A single intent'
                        ),
                        'An array of intents'
                ),
        ]);
    }

    /**
     * Return generated messages
     *
     * @return external_multiple_structure
     */
    public static function execute_returns() {
        return new external_multiple_structure(
                new external_value(PARAM_TEXT, "Generated message"));
    }

}
