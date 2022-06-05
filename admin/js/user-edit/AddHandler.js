import Swal from "sweetalert2";

const $ = jQuery;

export default class AddHandler {
    constructor(userID, referrerID) {
        this.userID = userID;
        this.referrerID = referrerID;
    }

    sendRequest(successCallback, failCallback) {

        $.post(ajaxurl, this.getData(), (response) => {
            if (response.success) {
                successCallback(response);
            } else {
                failCallback(response);
            }
        }).fail(this.onRequestFailure);

    }

    getData() {
        return {
            action: 'wp_referral_code_add_user_relation',
            user_id: this.userID,
            referrer_id: this.referrerID,
            nonce: WPReferralCode.nonceAdd,
        }
    };


    onRequestFailure(err) {
        // todo:
    }

}