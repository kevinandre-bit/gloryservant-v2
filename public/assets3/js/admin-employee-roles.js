// Employee roles page logic
(function(){
  if (window.jQuery) {
    jQuery(function($){
      if ($('#dataTables-example').length) {
        $('#dataTables-example').DataTable({
          responsive: true,
          pageLength: 15,
          lengthChange: false,
          searching: true,
          ordering: true
        });
      }
      
      $('.ui.dropdown.scope_level').dropdown();

      $('.btn-edit-role').on('click', function(){
        var id = $(this).attr('data-id');
        var url = $("#_url").val();
        $.ajax({
          url: url + '/user/roles/get/',
          type: 'get',
          dataType: 'json',
          data: { id: id },
          headers: { 'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
          success: function(response) {
            var $state = response['state'];
            var $scope = response['scope_level'] || 'all';
            $('.edit input[name="id"]').val(response['id']);
            $('.edit input[name="role_name"]').val(response['role_name']);
            if ($state == 'Active') {
              $('.ui.dropdown.state').dropdown({values:[{name:'Active',value:'Active',selected:true},{name:'Disabled',value:'Disabled'}]});
            } else if ($state == 'Disabled') {
              $('.ui.dropdown.state').dropdown({values:[{name:'Active',value:'Active'},{name:'Disabled',value:'Disabled',selected:true}]});
            }
            $('.ui.dropdown.scope_level').dropdown('set selected', $scope);
            $('.ui.modal.edit').modal('show');
          }
        });
      });
    });
  }
})();

