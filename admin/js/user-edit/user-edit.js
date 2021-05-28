import AjaxButton from "../AjaxButton";
import DeleteHandler from "./DeleteHandler";

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
});