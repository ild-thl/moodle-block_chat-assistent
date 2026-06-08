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
 * Plugin upgrade steps are defined here.
 *
 * @package     block_sidekick
 * @category    upgrade
 * @copyright   2026 Your Name <you@example.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */



defined('MOODLE_INTERNAL') || die();

$functions = [
        'block_sidekick_get_next_assignment' => [
                'classname' => 'block_sidekick\external\get_next_assignment',
                'description' => 'Finds the next assignment in a course.',
                'type' => 'read',
                'ajax' => true,
        ],
        'block_sidekick_send_prompt' => [
                'classname' => 'block_sidekick\external\send_prompt',
                'description' => 'Toggle the user preferences of the plugin',
                'type' => 'read',
                'ajax' => true,
        ],
];
