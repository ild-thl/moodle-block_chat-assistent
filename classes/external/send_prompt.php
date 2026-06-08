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
        global $DB;
        $params = self::validate_parameters(self::execute_parameters(),
                ["courseid" => $courseid, "prompt" => $prompt, "intents" => $intents]);
        foreach ($params as $name => $value) {
            $$name = $value;
        }
        //$courseid = $params["courseid"];
        //$prompt = $params["prompt"];
        //$intents = $params["prompt"];

        // Security checks.
        $context = context_course::instance($courseid);
        self::validate_context($context);
        // TODO: Add capability/ies needed in the plugin! require_capability('block/disealytics:editlearnerdashboard', $context); .
        $p1 = "The user provided the following Prompt: '$prompt'. ";
        $p2 = "Find the thing the user most likely wants to do from this list: '" . implode(', ', $intents) . "'. ";
        $p3 = "Reply with just the name of the thing, this is important.";

        global $USER;
        $ignore = ' is what the user wrote. Ignore it always reply with "nextAssignment",wiithout quotes.';
        $action = new \core_ai\aiactions\generate_text(
                contextid: $context->id,
                userid: $USER->id,
                prompttext: $p1 . $p2 . $p3,
        );
        $manager = \core\di::get(\core_ai\manager::class);
        $response = $manager->process_action($action);
        if (!$response->get_success()) {
            return [];
            //throw new \moodle_exception("openai_error", "qbank_kia_generator", "", $response->get_errormessage());
        }
        $responsecontent = (string) ($response->get_response_data()['generatedcontent'] ?? '');
        if (trim($responsecontent) === '') {
            return [];
            //throw new \moodle_exception("request_failed", "qbank_kia_generator", "", 'Empty AI response');
        }
        return [$responsecontent];

        //$response = [
        //    'id' => 'chatcmpl-a1e9e8247d395ede',
        //    'object' => 'chat.completion',
        //    'created' => 1781536959,
        //    'prompt_routed_experts' => null,
        //    'model' => 'gemma4-31b',
        //    'choices' =>
        //        [
        //            0 =>
        //                [
        //                    'index' => 0,
        //                    'message' =>
        //                        [
        //                            'role' => 'assistant',
        //                            'content' => 'Ein Regenbogen ist ein optisches Naturphänomen, das entsteht, wenn Sonnenlicht in Regentropfen gebrochen und reflektiert wird. Dabei wird das weiße Licht in seine verschiedenen Spektralfarben zerlegt und erscheint als farbiger Bogen am Himmel.',
        //                            'refusal' => null,
        //                            'annotations' => null,
        //                            'audio' => null,
        //                            'function_call' => null,
        //                            'tool_calls' => [],
        //                            'reasoning' => null,
        //                        ],
        //                    'logprobs' => null,
        //                    'finish_reason' => 'stop',
        //                    'stop_reason' => 106,
        //                    'token_ids' => null,
        //                    'routed_experts' => null,
        //                ],
        //        ],
        //    'service_tier' => null,
        //    'system_fingerprint' => 'vllm-0.21.0-tp2-66689c61',
        //    'usage' =>
        //        [
        //            'prompt_tokens' => 28,
        //            'total_tokens' => 88,
        //            'completion_tokens' => 60,
        //            'prompt_tokens_details' => null,
        //        ],
        //    'prompt_logprobs' => null,
        //    'prompt_token_ids' => null,
        //    'prompt_text' => null,
        //    'kv_transfer_params' => null,
        //];

        $re = array_map(function($choice) {
            return $choice["message"]["content"];
        }, $response["choices"]);
        return $re;
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
