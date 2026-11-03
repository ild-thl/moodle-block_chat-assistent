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
 * TODO describe module textinput.
 *
 * @module    block_sidekick/chips/textinput.
 * @copyright 2026 Your Name
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {sendPrompt} from "block_sidekick/repository";
import {intents, showResponse} from "block_sidekick/content";

/**
 * Handles form validation and submission for the assistant input.
 *
 * @param {int} courseid
 * @returns {Promise<void>}
 */
export async function entry(courseid) {
    const form = document.getElementById('sidekickAssistantForm');
    const input = document.getElementById('assistField');
    const errorSpan = document.getElementById('assistFieldError');

    // Form submission handler with validation
    form.addEventListener('submit', (event) => {
        event.preventDefault();

        const message = input.value.trim();

        // Validate input
        if (!message) {
            // Show error
            input.setAttribute('aria-invalid', 'true');
            errorSpan.classList.remove('hidden');
            input.focus();
            return;
        }

        // Clear any existing error
        input.setAttribute('aria-invalid', 'false');
        errorSpan.classList.add('hidden');

        handleChatbox(courseid, message);
    });

    // Clear error when user starts typing
    input.addEventListener('input', () => {
        if (input.value.trim()) {
            input.setAttribute('aria-invalid', 'false');
            errorSpan.classList.add('hidden');
        }
    });
}

/**
 *
 * @param {int} courseid
 * @param {string} prompt
 * @returns {Promise<void>}
 */
async function handleChatbox(courseid, prompt) {
    if (prompt === "") {
        return;
    }
    let answer = await sendPrompt(courseid, prompt, [...intents.keys()]);
    showResponse(answer);
}
