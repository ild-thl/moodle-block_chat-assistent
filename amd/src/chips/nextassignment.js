import {highlight, registerIntent, runIntent, scrollTo} from "block_sidekick/content";
import {fetchCourseAssignments} from "block_sidekick/repository";

/**
 *
 * @param {int} courseid
 * @returns {Promise<void>}
 */
export async function entry(courseid) {

    registerIntent("nextAssignment", ["assignments"], nextAssignment);

    document.getElementById('nextAssignment').addEventListener('click', async () => {
        await runIntent('nextAssignment', [courseid]);
    });
    //console.log(await test(courseid));

}

/**
 *
 * @param {int} courseid CourseID
 * @returns {Promise<Object>}
 */
async function nextAssignment(courseid) {
    let assignid = JSON.parse(await fetchCourseAssignments(courseid));
    if (!assignid) {
        return "Keine nächste Abgabe gefunden!";
    } else {
        const target = document.querySelector(`.activity.assign.modtype_assign[data-id="${assignid}"]`);
        if (target) {
            scrollTo(target);
            highlight(target, "Hier!");
        }
        return 'Deine nächste Abgabe ist hier.';

    }
}


