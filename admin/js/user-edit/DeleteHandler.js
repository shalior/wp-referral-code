import Swal from "sweetalert2";

const $ = jQuery;
export default class DeleteHandler {
    constructor(userID, referrerID) {
        this.userID = userID;
        this.referrerID = referrerID;
    }

    sendRequest(successCallback, failCallback) {
        Swal.fire({
            title: WPReferralCode.alert.title,
            text: WPReferralCode.alert.text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: WPReferralCode.alert.confirmText,
            cancelButtonText: WPReferralCode.alert.cancelText
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(ajaxurl, this.getData(), (response) => {
                    if (response.success) {
                        successCallback(response);
                        this.onSuccess();
                    } else {
                        failCallback(response);
                    }
                }).fail(this.onRequestFailure);
            } else {
                failCallback({event: "canceled"})
            }
        });

    }

    getData() {
        return {
            action: 'wp_referral_code_delete_user_relation',
            user_id: this.userID,
            referrer_id: this.referrerID,
            nonce: WPReferralCode.nonce
        }
    };

    onSuccess() {
        Swal.fire(
            WPReferralCode.confirmedAlert.title,
            WPReferralCode.confirmedAlert.text,
            'success'
        );
    }

    onFailure(response) {

    }

    onRequestFailure(err) {

    }

}




