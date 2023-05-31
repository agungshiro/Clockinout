$(document).ready(function() {

    /**
     * Delete a user
     */
    $('.btn-delete-user').click(function() {
        window.location.href = "/Clockinout/admin/restaurants/delete/" + $(this).attr('data-id');
    });

    $('.clockpick').clockpicker();
        

});