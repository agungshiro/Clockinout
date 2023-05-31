$(document).ready(function() {

    /**
     * Delete a user
     */
    $('.btn-delete-user').click(function() {
        window.location.href = "/Clockinout/admin/employee/delete/" + $(this).attr('data-id');
    });

});