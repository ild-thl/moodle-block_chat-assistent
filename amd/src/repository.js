/**
 * Bundles code for ajax calls.
 * @module block_sidekick/repository
 */

import {call as makeCalls} from 'core/ajax';

export const fetchCourseAssignments = (courseid) => makeCalls([{
    methodname: 'block_sidekick_get_next_assignment',
    args: {
        courseid: courseid,
    },
}])[0];

export const findIntentAI = (courseid, prompt, intents) => makeCalls([{
    methodname: 'block_sidekick_send_prompt',
    args: {
        courseid: courseid,
        prompt: prompt,
        intents: intents,
    },
}])[0];
