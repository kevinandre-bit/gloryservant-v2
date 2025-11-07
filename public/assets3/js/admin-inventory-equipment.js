// Inventory Equipment page scripts
(function(){
  // Flash notify via hidden data element
  document.addEventListener('DOMContentLoaded', function(){
    var flash = document.getElementById('flash-data');
    if (flash && window.Lobibox && typeof Lobibox.notify === 'function') {
      var msg = flash.getAttribute('data-success');
      if (msg) {
        Lobibox.notify('success', {
          pauseDelayOnHover: true,
          continueDelayOnInactiveTab: false,
          position: 'top right',
          icon: 'bi bi-check2-circle',
          msg: msg
        });
      }
    }
  });

  // Cropper
  (function(){
    let cropper;
    const input = document.getElementById('photo');
    const preview = document.getElementById('preview');

    if (!input || !preview || typeof Cropper === 'undefined') return;

    input.addEventListener('change', e => {
      const file = e.target.files && e.target.files[0];
      if (!file) return;

      const url = URL.createObjectURL(file);
      preview.src = url;
      preview.style.display = 'block';

      if (cropper) cropper.destroy();
      cropper = new Cropper(preview, {
        aspectRatio: 1,
        viewMode: 1,
        autoCropArea: 1,
        crop(event) {
          const det = event.detail || {};
          const set = (id,val)=>{ var el=document.getElementById(id); if (el) el.value = Math.round(val||0); };
          set('crop_x', det.x); set('crop_y', det.y); set('crop_width', det.width); set('crop_height', det.height);
        },
      });
    });
  })();

  // DataTables
  if (window.jQuery) {
    jQuery(function($){
      if ($('#example').length) { $('#example').DataTable(); }
      if ($('#example2').length) {
        var table = $('#example2').DataTable({
          pageLength: 15,
          lengthChange: false,
          buttons: [ 'copy', 'excel', 'pdf', 'print']
        });
        table.buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
      }
    });
  }
})();

