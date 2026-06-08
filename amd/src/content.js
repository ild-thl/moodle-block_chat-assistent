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
 * TODO describe module content
 *
 * @module    block_sidekick/content
 * @copyright 2026 Your Name
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 *
 */
export async function entry() {
}

/* =================================================================
   SCHICHT 3 — AKTION
   Generische DOM-Aktion: zum Element scrollen und hervorheben.
   Wird von allen Intents gemeinsam genutzt.
   ================================================================= */
/**
 *
 * @param {HTMLElement} el
 * */
export function scrollTo(el) {
    el.scrollIntoView({behavior: 'smooth', block: 'center'});
}

/**
 * Highlights target with a floating text.
 * @param {HTMLElement} target
 * @param {string} calloutText
 */
export function highlight(target, calloutText) {
    target.classList.remove('block-sidekick-highlight');

    requestAnimationFrame(() => {
        target.classList.add('block-sidekick-highlight');
    });
    // void target.offsetWidth;            // Reflow erzwingen, damit Animation neu startet
    // target.classList.add('block-sidekick-highlight');
    if (calloutText) {
        const old = target.querySelector('.lp-callout');
        if (old) {
            old.remove();
        }
        const tag = document.createElement('div');
        tag.className = 'block-sidekick-callout';
        tag.textContent = calloutText;
        target.appendChild(tag);
        setTimeout(() => tag.remove(), 2600);
    }
    setTimeout(() => target.classList.remove('block-sidekick-highlight'), 2400);
}

export const intents = new Map();

/**
 * Registers intents to the intent map. Components use this to "announce" intents they can run
 * so they can be reused across components.
 * @param {string} name
 * @param {Array<string>} keywords
 * @param {function} func
 */
export function registerIntent(name, keywords, func) {
    intents.set(name, {keywords: keywords, func: func});
}

/**
 * Tries to run the specified intent from the intents-map.
 * @param {string} name
 * @param {array} args
 */
export async function runIntent(name, args) {
    let intent;
    if ((intent = intents.get(name))) {
        let result = await intent.func(...args);
        showResponse(result);
    } else {
        showResponse('Das habe ich noch nicht verstanden. Probier z. B. „Wann sind meine nächsten Abgaben?"');
    }
}

// /**
//  *
//  * @param {Array<{name: string, args: Array<*>}>} functions
//  * @returns {Map<string, *>}
//  */
// export async function runMultiple(functions) {
//     let results = await Promise.all(functions.map(func => intents.get(func.name).func(...func.args)));
//     return new Map(
//         functions.map((func, i) => [func.name, results[i]])
//     );
// }

/* =================================================================
   SCHICHT 1 — AUSLÖSER
   a) Button -> runIntent(name) direkt
   b) Freitext -> handleQuery() ermittelt den Intent ueber Keywords
   Beide enden in der gleichen Aktion.
   =================================================================
*/

/**
 *
 * @param {string} html
 */
export function showResponse(html) {
    const box = document.getElementById('assistResponse');
    box.innerHTML = html;
    box.classList.add('show');
}
