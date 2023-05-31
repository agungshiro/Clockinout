$(document).ready(function() {

    /**
     * Delete a user
     */
    $('.btn-delete-user').click(function() {
        window.location.href = "/Clockinout/admin/scheduling/delete/" + $(this).attr('data-id');
    });

});