var Session = {

    initialize: function($wrapper) {
        this.$wrapper = $wrapper;
        this.$wrapper.find('.js-delete-session').on(
            'click',
            this.handleSessionDelete
        );
    },

    handleSessionDelete: function(e) {
        e.preventDefault();

        $(this).addClass('text-danger');

        $(this).find('.fas')
        .removeClass('fa-skull-crossbones')
            .addClass('fa-spinner')
            .addClass('fa-spin');

        var $row = $(this).closest('tr');
        var deleteUrl = $(this).data('url');
        
        $.ajax({
            url: deleteUrl,
            method: 'DELETE',
            success: function() {
                $row.fadeOut();
            }
        });
    },

};


$(document).ready(function() {

    var $table = $('.js-sessions-table');
    Session.initialize($table);

    /*

    var $table = $('.js-sessions-table');

    $table.find('.js-delete-session').on('click', function(e) {
        e.preventDefault();

        $(this).addClass('text-danger');
        $(this).find('.fas')
            .removeClass('fa-skull-crossbones')
            .addClass('fa-spinner')
            .addClass('fa-spin');   
        
        var $row = $(this).closest('tr');
        var deleteUrl = $(this).data('url');
        
        $.ajax({
            url: deleteUrl,
            method: 'DELETE',
            success: function() {
                $row.fadeOut();
            }
        });
    });
  
//    */

});   




