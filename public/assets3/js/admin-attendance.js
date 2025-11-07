// Attendance page modal populate
(function(){
  document.addEventListener('DOMContentLoaded', function() {
    var formModal = document.getElementById('FormModal');
    if (!formModal) return;
    formModal.addEventListener('show.bs.modal', function(event) {
      var btn    = event.relatedTarget;
      var modal  = this;

      var id           = btn.getAttribute('data-id');
      var idno         = btn.getAttribute('data-idno');
      var name         = btn.getAttribute('data-name');
      var date         = btn.getAttribute('data-date');
      var timeinDate   = btn.getAttribute('data-timein_date');
      var timein       = btn.getAttribute('data-timein');
      var timeoutDate  = btn.getAttribute('data-timeout_date');
      var timeout      = btn.getAttribute('data-timeout');
      var reason       = btn.getAttribute('data-reason');

      document.getElementById('modalId').value = id;
      modal.querySelector('#modalIdno').value       = idno;
      modal.querySelector('#modalName').value       = name;
      modal.querySelector('#modalDate').value       = timeinDate || date;
      modal.querySelector('#modalTimeIn').value     = timein || '';
      modal.querySelector('#modalTimeOut').value    = timeout || '';
      modal.querySelector('#modalReason').value     = reason || '';
    });
  });
})();

