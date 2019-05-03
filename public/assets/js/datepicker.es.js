jQuery(document).ready(function() {
    $('.js-datepicker').datepicker({
        format: 'dd/mm/yyyy',
        weekStart: 1,
        todayBtn: "linked",
        language: "es",
        daysOfWeekHighlighted: "0,6",
        autoclose: true
    });
});