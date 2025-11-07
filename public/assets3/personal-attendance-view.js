document.addEventListener('DOMContentLoaded', function(){
  // Geolocation check for clock in
  window.checkClockInLocation = function(){
    if (!navigator.geolocation) { alert('Geolocation is not supported by this browser.'); return; }
    navigator.geolocation.getCurrentPosition(function(pos){
      var latitude = pos.coords.latitude, longitude = pos.coords.longitude;
      var csrf = document.querySelector('meta[name="csrf-token"]');
      var token = csrf ? csrf.getAttribute('content') : '';
      if (!window.jQuery) return;
      jQuery.ajax({
        url: '/check-location', type: 'POST', data:{ latitude: latitude, longitude: longitude, _token: token },
        success: function(resp){
          if (resp && resp.allowed) { alert('Clock-in successful!'); } else { alert('You are outside of the allowed clock-in area.'); }
        }, error: function(){ alert('Unable to check location.'); }
      });
    }, function(){ alert('Unable to retrieve your location. Please allow location access.'); });
  };

  // DataTables + datepicker filter
  if (window.jQuery) {
    var $ = jQuery;
    if ($.fn.DataTable) {
      $('#dataTables-example').DataTable({responsive: true,pageLength: 15,lengthChange: false,searching: false,ordering: true});
    }
    if ($.fn.datepicker) {
      $('.airdatepicker').datepicker({ language: 'en', dateFormat: 'yyyy-mm-dd' });
    }
    $('#filterform').on('submit', function(e){
      e.preventDefault();
      var date_from = $('#datefrom').val();
      var date_to = $('#dateto').val();
      var url = $('#_url').val();
      var csrf = $('meta[name="csrf-token"]').attr('content');

      $.ajax({ url: url + '/get/personal/attendance/', type:'get', dataType:'json', data:{datefrom:date_from, dateto:date_to}, headers:{ 'X-CSRF-Token': csrf },
        success: function(response){
          var tbody = $('#dataTables-example tbody');
          if ($.fn.DataTable) $('#dataTables-example').DataTable().destroy();
          tbody.children('tr').remove();
          for (var i=0;i<response.length;i++){
            var emp = response[i];
            var in_color = (emp.status_timein === 'Late In') ? 'orange' : 'blue';
            var out_color = (emp.status_timeout === 'Early Out') ? 'red' : 'green';
            var intime = emp.timein || '', outime = emp.timeout || '';
            var totalh = emp.totalhours || '';
            tbody.append('<tr>'+
              '<td>'+ emp.date +'</td>'+
              '<td>'+ intime +'</td>'+
              '<td>'+ outime +'</td>'+
              '<td>'+ totalh +'</td>'+
              '<td><span class="ui label '+in_color+'">'+(emp.status_timein||'')+'</span> | <span class="ui label '+out_color+'">'+(emp.status_timeout||'')+'</span></td>'+
            '</tr>');
          }
          if ($.fn.DataTable) $('#dataTables-example').DataTable({responsive: true,pageLength: 15,lengthChange: false,searching: false,ordering: true});
        }
      });
    });
  }
});

