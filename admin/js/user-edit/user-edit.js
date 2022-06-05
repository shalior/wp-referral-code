import AjaxButton from "../AjaxButton";
import DeleteHandler from "./DeleteHandler";
import AddHandler from "./AddHandler";
import Swal from "sweetalert2";

const $ = jQuery;

$(document).ready(() => {
    $('.wrc-remove-relation').on('click', function (e) {
        e.preventDefault();
        const ajaxButton = new AjaxButton($(this), new DeleteHandler($(this).data('user-id'), $(this).data('referrer-id')))
        ajaxButton.disable().loading(null);
        ajaxButton.handle(response => {
            ajaxButton.disable();
            ajaxButton.button.parent().remove();
        }, response => {
            if (response.event === "canceled") {
                ajaxButton.enable().changeTextTo('Remove');
                return;
            }
            ajaxButton.changeTextTo('Failed!');
        });
        // send an ajax request to delete relation

    });

    const addRelationButton = $('#wrc-add-rel-button');
    addRelationButton.attr("disabled", true); // disable by default
    const searchUserSelect = $('#wrc-search-user-select');
    // enable button on user select
    searchUserSelect.on('select2:select', function (e) {
        const userID = e.params.data.id; // this is the user_id

        if (userID == -1) {
            addRelationButton.attr("disabled", true);
        } else {
            addRelationButton.attr("disabled", false);
        }
    });

    addRelationButton.on('click', function (e) {
        e.preventDefault();

        const referrerID = $(this).data('referrer-id');
        let toAddUserId = searchUserSelect.val();

        const addRelationAjaxButton = new AjaxButton($(this), new AddHandler(toAddUserId, referrerID));

        addRelationAjaxButton.disable();

        addRelationAjaxButton.handle(response => {
            window.location.reload();
        }, response => {
            addRelationAjaxButton.enable();
            Swal.fire(
                WPReferralCode.alert.error,
                response.data.error,
                'error'
            );
        });


    });

});