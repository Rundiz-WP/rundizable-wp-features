/* 
 * @license http://opensource.org/licenses/MIT MIT
 */


// Hide website field on user profile
const url = document.querySelector('#url');
if (url) {
    const trHTML = url.closest('tr');
    if (trHTML) {
        trHTML.style.setProperty('display', 'none', 'important');
    }
}