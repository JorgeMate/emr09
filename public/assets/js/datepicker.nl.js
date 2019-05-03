jQuery(document).ready(function() {
    $('.js-datepicker').datepicker({
        format: "dd/mm/yyyy",
        weekStart: 1,
        todayBtn: "linked",
        language: "nl",
        daysOfWeekHighlighted: "0,6",
        autoclose: true
    });
});