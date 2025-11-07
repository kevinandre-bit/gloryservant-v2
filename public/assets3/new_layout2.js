document.addEventListener('DOMContentLoaded', function () {
  try {
    var flash = document.getElementById('flash-data');
    if (flash) {
      var success = flash.getAttribute('data-success');
      var info = flash.getAttribute('data-info');
      var error = flash.getAttribute('data-error');
      var errorsJson = flash.getAttribute('data-errors');
      var errors = [];
      try { errors = errorsJson ? JSON.parse(errorsJson) : []; } catch (e) {}

      if (typeof Lobibox !== 'undefined') {
        if (success) Lobibox.notify('success', { pauseDelayOnHover: true, continueDelayOnInactiveTab: false, position: 'top center', icon: 'bi bi-check2-circle', msg: success });
        if (info) Lobibox.notify('error', { pauseDelayOnHover: true, continueDelayOnInactiveTab: false, position: 'top center', icon: 'bi bi-exclamation-octagon', msg: info });
        if (error) Lobibox.notify('error', { pauseDelayOnHover: true, continueDelayOnInactiveTab: false, position: 'top center', icon: 'bi bi-x-circle', msg: error });
        if (Array.isArray(errors)) {
          errors.forEach(function (msg) {
            Lobibox.notify('error', { pauseDelayOnHover: true, continueDelayOnInactiveTab: false, position: 'top center', icon: 'bi bi-x-circle', msg: msg });
          });
        }
      }
    }

    if (typeof feather !== 'undefined') {
      feather.replace();
    }

    if (typeof PerfectScrollbar !== 'undefined') {
      var el = document.querySelector('.user-list');
      if (el) new PerfectScrollbar('.user-list');
    }

    if (window.jQuery && jQuery.fn.peity) {
      jQuery('.data-attributes span').peity('donut');
    }

    if (window.flatpickr) {
      var $ = window.jQuery || function(s){return document.querySelectorAll(s)};
      // datepicker
      var dp = document.querySelectorAll('.datepicker');
      dp && dp.forEach(function(el){ flatpickr(el); });

      var tp = document.querySelectorAll('.time-picker');
      tp && tp.forEach(function(el){ flatpickr(el, { enableTime: true, noCalendar: true, dateFormat: 'G:i K', time_24hr: false }); });

      var dt = document.querySelectorAll('.date-time');
      dt && dt.forEach(function(el){ flatpickr(el, { enableTime: true, dateFormat: 'Y-m-d H:i' }); });

      var df = document.querySelectorAll('.date-format');
      df && df.forEach(function(el){ flatpickr(el, { altInput: true, altFormat: 'F j, Y', dateFormat: 'Y-m-d' }); });

      var dr = document.querySelectorAll('.date-range');
      dr && dr.forEach(function(el){ flatpickr(el, { mode: 'range', altInput: true, altFormat: 'F j, Y', dateFormat: 'Y-m-d' }); });

      var di = document.querySelectorAll('.date-inline');
      di && di.forEach(function(el){ flatpickr(el, { inline: true, altInput: true, altFormat: 'F j, Y', dateFormat: 'Y-m-d' }); });
    }

    if (window.jQuery) {
      jQuery(document).ajaxError(function (event, xhr) {
        if (xhr && xhr.status === 403) {
          var modal = document.getElementById('deniedModal');
          if (modal && window.bootstrap) {
            new bootstrap.Modal(modal).show();
          }
        }
      });
    }
  } catch (e) {
    // fail-safe: never break the page if optional libs aren't present
    console && console.warn && console.warn('init error', e);
  }
});

