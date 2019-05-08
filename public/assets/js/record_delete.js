var Record = {

    initialize: function($wrapper) {
        this.$wrapper = $wrapper;
        this.$wrapper.find('.js-delete-record').on(
            'click',
            this.handleRecordDelete
        );
    },

    handleRecordDelete: function(e) {
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

    var $table = $('.js-records-table');
    Record.initialize($table);

});   




