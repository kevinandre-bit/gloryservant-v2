document.addEventListener('DOMContentLoaded', function(){
  if (!window.jQuery) return;
  var $ = jQuery;
  if ($.fn.DataTable) {
    $('#dataTables-example').DataTable({responsive:true,pageLength:15,lengthChange:false,searching:false,ordering:true});
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
    $.ajax({ url: url + '/get/personal/schedules', type:'get', dataType:'json', data:{datefrom:date_from, dateto:date_to}, headers:{'X-CSRF-Token': csrf},
      success: function(response){
        var tbody = $('#dataTables-example tbody');
        if ($.fn.DataTable) $('#dataTables-example').DataTable().destroy();
        tbody.children('tr').remove();
        for (var i=0;i<response.length;i++){
          var r = response[i];
          var statusText = r.archive === '0' ? 'Present Schedule' : 'Past Schedule';
          var statusClass = r.archive === '0' ? 'green' : 'teal';
          tbody.append('<tr>'+
            '<td>'+ (r.intime || '') +'</td>'+
            '<td>'+ (r.outime || '') +'</td>'+
            '<td>'+ (r.hours || '') +' hours</td>'+
            '<td>'+ (r.restday || '') +'</td>'+
            '<td>'+ (r.datefrom || '') +'</td>'+
            '<td>'+ (r.dateto || '') +'</td>'+
            '<td><span class="'+statusClass+'">'+statusText+'</span></td>'+
          '</tr>');
        }
        if ($.fn.DataTable) $('#dataTables-example').DataTable({responsive:true,pageLength:15,lengthChange:false,searching:false,ordering:true});
      }
    });
  });
});

