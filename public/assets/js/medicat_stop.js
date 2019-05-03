var Medicat = {

    initialize: function($wrapper) {
        this.$wrapper = $wrapper;
        this.$wrapper.find('.js-stop-medicat').on(
            'click',
            this.handleStop
        );
    },

    handleStop: function(e) {
        e.preventDefault();

        $(this).addClass('text-danger');

        var $row = $(this).closest('tr');
        var stopUrl = $(this).data('url');
        $.ajax({
            url: stopUrl,
            method: 'POST',
            success: function() {
                $row.fadeOut();
            }
        });
    },

};

$(document).ready(function() {

    var $table = $('.js-medicats-table');
    Medicat.initialize($table);
    
});   

