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

        $(this).find('.fa')
        .removeClass('fa-trash')
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
    var $tableOperas = $('.js-operas-table');
    Record.initialize($tableOperas);

    var $tableImgs = $('.js-imgs-table');
    Record.initialize($tableImgs);

});   






