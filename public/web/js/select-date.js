/**
 * Created by PMTB on 1/17/2018.
 */
$(document).ready(function () {
    $('.select-date input').datepicker({
        format: "dd/mm/yyyy",
        todayBtn: "linked",
        autoclose: true,
        todayHighlight: true
    });
});