$(document).ready(function() {

    /**
     * Delete a user
     */
    $('.btn-delete-user').click(function() {
        window.location.href = "/Clockinout/admin/restaurants/delete_job/" + $(this).attr('data-id');
    });

    $('.clockpick').clockpicker();
        

});