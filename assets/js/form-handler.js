/**
 * TIAA WPPlugin - Elementor Forms Action Handler
 * TODO = probably need to remove this docblock as it's put into every browser download
 * This script customizes the behavior of Elementor Pro forms that use the TIAA form action.
 * It provides an interface for handling form submissions, sending data via the WordPress REST API,
 * and displaying relevant status messages depending on the outcome of the submission.
 *
 * Primary functionality:
 * - Hides and shows specific form elements (e.g., status divs) based on submission state.
 * - Prevents default form behavior and handles data submission via `wp.apiFetch`.
 * - Processes API responses to display success/error messages.
 * - Handles various error conditions (e.g., missing data, server errors, API issues).
 *
 * This script interacts with `tiaaPluginData`, a WordPress-localized script object containing
 * configuration data such as nonce and API endpoints.
 *
 * @package ElementorFormsTIAA
 * @subpackage TIAAWPPlugin
 * @since 0.0.3
 * @author Lew Grothe & the tiaa-forum.org Platform subteam
 * @link https://tiaa-forum.org/
 */
// seems pretty crude to slam the body tg around like this but our status divs get rendered before
// they're shut off so they flash before disappearing
document.write('<style>body { display: none; }</style>');

document.addEventListener('DOMContentLoaded', function () {
    const tiaaHideArray = document.querySelectorAll('.tiaa_status_hide');
    /*
     * this is required since elementor adds classes after the ones we set with
     * the text widget which breaks the expected CSS hierarchy
     */
    tiaaHideArray.forEach(element => {
        element.style.display = 'none';
    });
    document.body.style.display = 'block';
});
/**
 * Handles the form submission event for the TIAA Invite feature.
 *
 * This function listens for submission events specifically on forms with the ID
 * `#tiaa_invite_form`. It prevents the default submission behavior, processes
 * the form data, and sends it to the server via the WordPress REST API (`wp.apiFetch`).
 * Depending on the response from the server, it updates the UI by displaying appropriate
 * success or error messages.
 *
 * Key operations:
 * - Prevents default browser form submission behavior.
 * - Collects and converts form data into an object for the API request.
 * - Uses the `wp.apiFetch` utility to send data securely with nonce authorization.
 * - Handles various server responses and updates the UI with dynamic messages.
 * - Resets stateful elements to allow subsequent resubmissions.
 *
 * @param {Event} event - The submit event triggered by a form element.
 *
 * @listens submit
 * @since 0.0.3
 */
(function () {
    function attachListenerToForm() {
//        console.log('Attaching listener to form');
        const form = document.querySelector('#tiaa_invite_form');
        if (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                console.log('Form submitted');
                // we need to reset this in case someone re-uses the form without refreshing
                // the page
                const tiaaHideArray = document.querySelectorAll('.tiaa_status_hide');
                tiaaHideArray.forEach(element => {
                    element.style.display = 'none';
                });
//                const form = event.target;
                const formData = new FormData(form); // Collect form data
                // Convert FormData to a plain object
                const dataObject = {};
                formData.forEach((value, key) => {
                    dataObject[key] = value;
                });

                wp.apiFetch.use(wp.apiFetch.createNonceMiddleware(tiaaPluginData.nonce));
                // tiaaPluginData{} object is passed in from the plugin via WP localized script
                // in the WP plugin
                if (!tiaaPluginData || !tiaaPluginData.apiUrl || !tiaaPluginData.nonce) {
                    console.error('TIAA Plugin data is not properly initialized.');
                    return;
                }
                // Use wp.apiFetch to send the form data
                wp.apiFetch({
                    path: tiaaPluginData.apiUrl,
                    method: 'POST',
                    data: dataObject,
                })
                    .then((response) => {
                        try {
                            if (response.success && response.status === 200) {
                                if (response?.code === 'dropped_email') {
                                    const statusDiv = document.querySelector('.tiaa_dropped_msg');
                                    statusDiv.style.display = 'none';
                                } else {
                                    tiaa_show_email_msg('.tiaa_success_msg',
                                        dataObject['form_fields[email]'] || 'No email provided');
                                }
                            } else {
                                throw new Error(response.response || 'Unknown error');
                            }
                        } catch (error) {
                            // Handle parsing or processing errors
                            console.error("Error processing response: ", error);
                            tiaa_show_error_msg('.tiaa_error_unk_msg', error.message);
                        }
                    })
                    .catch((error) => {
                        let message = '';
                        // Handle errors and update the status div
                        if (error?.status === 403 || error.message?.includes('403')) {
                            message = 'A configuration error occurred.';
                            tiaa_show_error_msg('.tiaa_error_403_msg', message);
                        } else if (error.message?.includes('already a member')) {
                            let email = dataObject['form_fields[email]'];
                            tiaa_show_email_msg('.tiaa_duplicate_msg', email);
                        } else {
                            message = error.message || 'An unknown error occurred.';
                            tiaa_show_error_msg('.tiaa_error_unk_msg', message);
                        }
                    });
            }, true); // set capture mode
//            console.log('Form listener attached');
            return true;
        }
        return false;
    }

    window.attachListenerToForm = attachListenerToForm;
})();


// Try immediately after DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    if (attachListenerToForm()) return;

    // Set up observer if the form isn't there yet
    const observer = new MutationObserver(() => {
        if (attachListenerToForm()) {
            observer.disconnect();
        }
    });
    observer.observe(document.body, { childList: true, subtree: true });
});

/**
 * Displays a status message with the provided email address.
 *
 * This function updates the UI by showing a specific status message element and dynamically
 * embedding an email address within the message. It is used to inform the user about the
 * result of form submission, such as success or duplicate membership notification.
 *
 * @param {string} msgClass - A CSS selector for the target status message element to be displayed.
 * @param {string} email_value    - The email address to be displayed in the message.
 *
 * @example
 * // Example usage:
 * tiaa_show_email_msg('.tiaa_success_msg', 'user@example.com');
 *
 * @since 0.0.3
 */
function tiaa_show_email_msg(msgClass, email_value) {
    //   console.log('show_email: ' + msg_id);
    const divs = document.querySelectorAll(msgClass);
    if (divs.length > 1) {
        console.error('Multiple status divs found - expected only one');
    }
    const statusDiv = divs[0];
    if (statusDiv) {
        const emailDiv = statusDiv.querySelector('#email_value');
        if (emailDiv) {
            emailDiv.innerHTML = email_value;
        } else {
            console.log('emailDiv not found in ' + msgClass);
        }
        statusDiv.style.display = 'block';
     } else {
        console.log('statusDiv not found in ' + msgClass);
    }
}
/**
 * Displays an error message in the specified status element.
 *
 * This function locates a UI element via a CSS selector and updates its content
 * with the provided error message. It ensures the error message is visible to the user,
 * making it useful for conveying issues that occur during form submission or API interaction.
 *
 * @param {string} msgClass - A CSS selector for the target error message element to be updated and displayed.
 * @param {string} err_msg  - The error message to be displayed in the targeted element.
 *
 * @example
 * // Example usage:
 * tiaa_show_error_msg('#tiaa_error_403_msg', 'A configuration error occurred.');
 *
 * @since 0.0.3
 */
function tiaa_show_error_msg(msgClass,err_msg) {
    const divs = document.querySelectorAll(msgClass);
    if (divs.length > 1) {
        console.error('Multiple status divs found - expected only one');
    }
    const statusDiv = divs[0];
//    console.log('show_error: ' + msg_id);
    if (statusDiv) {
        const errDiv = statusDiv.querySelector('#tiaa_error_msg');
        if (errDiv) {
            errDiv.innerHTML = err_msg;
        } else {
            console.log('errDiv not found in ' + msgClass);
        }
        statusDiv.style.display = 'block';
    } else {
        console.log('statusDiv not found in ' + msgClass);
    }
}