requirejs( [
  '../typo3conf/ext/gtn_survey/Resources/Public/JavaScript/daterangepicker.js'
], function( Datepicker) {
    function initDatepicker() {
    $('.survey-date-range-picker').daterangepicker({
          "minYear": 2019,
          "timePicker": true,
          "timePicker24Hour": true,
          "timePickerIncrement": 15,
          "autoApply": true,
           "singleDatePicker":true,
          "minDate": "10/03/2019",
          startDate: new Date().getTime(), 
          endDate: new Date().getTime(),
          locale : {
             format : 'DD.MM.YYYY: HH:mm:ss'
         }
      }, function(start, end, label) {
        console.log('New date range selected: ' + start.format('YYYY-MM-DD hh:HH') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
      });
    }

initDatepicker();
});
