/**
 * Rundizable WP Features - Common JS use in admin area.
 * 
 * @package Rundizable-WP-Features
 * @since 2026-02-11
 * @since 1.0.3 This version name is for this plugin, the date value is from Rundiz Plugin Template.
 */


class RundizableWpFeaturesAdminCommon {


    /**
     * Handle response error. If response is error (for example, not 2xx) it will throw the error message to let `catch()` work.
     * 
     * @since 2026-02-11
     * @param {object} response The response object that have got from `rawResponse.json()`, or `rawResponse.text()` depend on content type.
     * @param {object} rawResponse Raw response from server.
     * @param {object} options The options:<br>
     *              `txtTryAgain` (string) The error "Please try again" message for use as default error message on status 403.<br>
     *              `txtErrorOccur` (string) The error "An error occur" message for all other error statuses.
     * @throws {Error} Throw the error on checked that HTTP response is error. 
     */
    ajaxHandleResponseError(response, rawResponse, options = {}) {
        if (typeof(options.txtTryAgain) !== 'string') {
            options.txtTryAgain = 'Please try again.';
        }
        if (typeof(options.txtErrorOccur) !== 'string') {
            options.txtErrorOccur = 'An error occur.';
        }

        const contentType = rawResponse.headers.get('Content-Type');

        if (403 === rawResponse.status) {
            let errorMessage = options.txtTryAgain;

            if (response?.data?.errorMessage) {
                errorMessage = response.data.errorMessage;
            }

            throw new Error(errorMessage);
        } else if (!rawResponse.ok) {
            if (contentType.toLowerCase().includes('text/html')) {
                throw new Error(response);
            } else if (contentType.toLowerCase().includes('application/json')) {
                let errorMessage = options.txtErrorOccur;

                if (typeof(response.data) === 'object' && typeof(response.data[0]) === 'object' && response.data[0]?.message) {
                    // if response use php `wp_send_json_error(new \WP_Error('err', 'Error message 1'), 500);`.
                    errorMessage = response.data[0].message;
                } else if (response?.message) {
                    // if response use php `throw new \Exception('Error message 2');`.
                    errorMessage = response.message;
                } else if (response?.data?.errorMessage) {
                    // if response use php `wp_send_json_error(['errorMessage' => 'Error message 3'], 500);`.
                    errorMessage = response.data.errorMessage;
                }

                throw new Error(errorMessage);
            }
        }// endif;
    }// ajaxHandleResponseError


    /**
     * Create alert HTML.
     * 
     * @since 2026-02-11
     * @param {string} message Notice message can be just text or HTML.
     * @param {string} status Alert status. Accepted: 'error', 'info', 'success', 'warning'.
     * @param {Boolean} isDismissible Is alert dismissible?
     * @param {string} dismissText Dismissable text. Example: 'Dismiss this notice'.
     * @returns {string} Return generated alert HTML.
     */
    createAlertHTML(message, status = 'error', isDismissible = false, dismissText = 'x') {
        if (typeof(message) !== 'string') {
            throw new Error('The argument `message` must be string, ' + typeof(message) + ' given.');
        }
        if (typeof(dismissText) !== 'string') {
            throw new Error('The argument `dismissText` must be string.');
        }

        const allowedStatuses = ['error', 'info', 'success', 'warning'];
        if (!allowedStatuses.includes(status)) {
            status = 'error';
        }
        if (typeof(isDismissible) !== 'boolean') {
            isDismissible = false;
        }

        let alertHTML = '<div class="notice notice-' + status;
        if (true === isDismissible) {
            alertHTML += ' is-dismissible';
        }
        alertHTML += '">';

        // search for block element in the message. -------------------
        const parser = new DOMParser();
        const messageDOM = parser.parseFromString(message, 'text/html');
        const blockElements = ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'address', 'article', 'aside', 'blockquote', 'div', 'footer', 'form', 'header', 'li', 'main', 'nav', 'ol', 'section', 'table', 'ul'];
        let foundBlockElement = false;
        const allElements = messageDOM.body.getElementsByTagName('*');
        for (let i = 0; i < allElements.length; i++) {
            const tagName = allElements[i].tagName.toLowerCase();
            if (blockElements.includes(tagName)) {
                foundBlockElement = true;
                break;
            }
        }// endfor;
        // end search for block element in the message. ---------------

        if (!foundBlockElement) {
            alertHTML += '<p>';
        }
        alertHTML += message;
        if (!foundBlockElement) {
            alertHTML += '</p>';
        }

        if (true === isDismissible) {
            alertHTML += '<button type="button" class="notice-dismiss" onclick="this.parentElement?.remove();"><span class="screen-reader-text">' + dismissText + '</span></button>';
        }
        alertHTML += '</div>';

        return alertHTML;
    }// createAlertHTML


    /**
     * Handle response error. If response is error (for example, not 2xx) it will throw the error message to let `catch()` work.
     * 
     * @see ajaxHandleResponseError()
     * @since 2026-02-11
     */
    static static_ajaxHandleResponseError(response, rawResponse, options = {}) {
        const thisClass = new this();
        return thisClass.ajaxHandleResponseError(response, rawResponse, options);
    }// static_ajaxHandleResponseError


    /**
     * Create alert HTML.
     * 
     * @see createAlertHTML()
     * @since 2026-02-11
     */
    static static_createAlertHTML(message, status = 'error', isDismissible = false, dismissText = 'x') {
        const thisClass = new this();
        return thisClass.createAlertHTML(message, status, isDismissible, dismissText);
    }// static_createAlertHTML


}// RundizableWpFeaturesAdminCommon
