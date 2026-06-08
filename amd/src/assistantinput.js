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

/**
 *
 * @param {int} courseid
 * @returns {Promise<void>}
 */
export async function entry(courseid) {

    //registerIntent('handleChatbox', ["chatbox"], handleChatbox);
    //registerIntent('guessIntent', ['intents'], guessIntent);

    document.getElementById('assistField').form.addEventListener('submit', (event) => {
        event.preventDefault();

        const form = event.currentTarget;
        const message = form.assistField.value;

        handleChatbox(courseid, message);
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
    // let guessedIntent = await findIntentAI(courseid, prompt, [...intents.keys()]);
    // await runIntent(guessedIntent[0],[courseid]);
}
