// New Employee page scripts
(function(){
  // Cropper modal flow
  document.addEventListener('DOMContentLoaded', function(){
    const input    = document.getElementById('imageInput');
    const modalEl  = document.getElementById('cropModal');
    if (!input || !modalEl || typeof bootstrap === 'undefined') return;

    const modal    = new bootstrap.Modal(modalEl);
    const img      = document.getElementById('cropImage');
    const out      = document.getElementById('imageCropped');
    const preview  = document.getElementById('avatarPreview');

    let cropper = null;
    let objectUrl = null;

    input.addEventListener('change', () => {
      const file = input.files?.[0];
      if (!file) return;
      if (!/^image\/(png|jpe?g)$/i.test(file.type)) { alert('Choose PNG/JPEG'); input.value=''; return; }

      if (objectUrl) URL.revokeObjectURL(objectUrl);
      objectUrl = URL.createObjectURL(file);
      img.src = objectUrl;
      modal.show();
    });

    const initCropper = () => {
      if (typeof Cropper === 'undefined') return;
      cropper?.destroy();
      cropper = new Cropper(img, {
        aspectRatio: 1,
        viewMode: 1,
        dragMode: 'move',
        autoCropArea: 1,
        background: false,
        responsive: true,
      });
    };

    modalEl.addEventListener('shown.bs.modal', () => {
      if (img.complete && img.naturalWidth > 0) {
        setTimeout(initCropper, 150);
      } else {
        img.onload = () => setTimeout(initCropper, 150);
      }
    });

    modalEl.addEventListener('hidden.bs.modal', () => {
      cropper?.destroy(); cropper = null;
      if (objectUrl) { URL.revokeObjectURL(objectUrl); objectUrl = null; }
    });

    // Controls
    const id = s => document.getElementById(s);
    id('btnZoomIn')?.addEventListener('click', ()=> cropper?.zoom(0.1));
    id('btnZoomOut')?.addEventListener('click', ()=> cropper?.zoom(-0.1));
    id('btnRotateLeft')?.addEventListener('click', ()=> cropper?.rotate(-90));
    id('btnRotateRight')?.addEventListener('click', ()=> cropper?.rotate(90));
    id('btnReset')?.addEventListener('click', ()=> cropper?.reset());

    id('btnCropSave')?.addEventListener('click', () => {
      if (!cropper) return;
      const canvas = cropper.getCroppedCanvas({ width: 512, temperament: 512, fillColor:'#fff' });
      const dataUrl = canvas.toDataURL('image/jpeg', 0.92);
      if (out) out.value = dataUrl;
      if (preview) { preview.src = dataUrl; preview.style.display = 'inline-block'; }
      modal.hide();
    });
  });

  // Stepper, datepicker, dropdown filtering
  document.addEventListener('DOMContentLoaded', function () {
    if (window.Stepper) {
      var ste = document.querySelector('#stepper1');
      if (ste) {
        // eslint-disable-next-line no-undef
        window.stepper1 = new Stepper(ste);
      }
    }

    // Re-init Air Datepicker when panes show
    document.querySelectorAll('.bs-stepper-pane').forEach(function(pane){
      pane.addEventListener('shown.bs-stepper', function(){
        if (window.$) {
          $(pane).find('.airdatepicker').each(function(){
            if (!$(this).data('airdp-init')) {
              $(this).data('airdp-init', true).datepicker({ position: $(this).data('position') || 'top right' });
            }
          });
        }
      });
    });

    // Ministry → filter Job Title options
    const ministrySelect = document.querySelector('select.ministry');
    const jobSelect = document.querySelector('select.jobposition');
    const allJobOptions = jobSelect ? Array.from(jobSelect.options) : [];

    function filterJobs() {
      if (!jobSelect) return;
      const dept = (ministrySelect?.value || '').toUpperCase();
      jobSelect.innerHTML = '';
      const placeholder = document.createElement('option');
      placeholder.value = '';
      placeholder.textContent = 'Select Job Title';
      jobSelect.appendChild(placeholder);
      allJobOptions.forEach(opt => {
        const dataDept = (opt.getAttribute('data-dept') || '').toUpperCase();
        if (!dataDept || dataDept === dept) {
          if (opt.value !== '') jobSelect.appendChild(opt.cloneNode(true));
        }
      });
    }

    if (ministrySelect && jobSelect) {
      filterJobs();
      ministrySelect.addEventListener('change', filterJobs);
    }
  });

  // jQuery datepicker and dropdown script (legacy)
  if (window.jQuery) {
    jQuery(function($){
      $('.airdatepicker').datepicker({ language: 'en', dateFormat: 'yyyy-mm-dd', autoClose: true });
      $('.ui.dropdown.ministry').dropdown({
        onChange: function(value) {
          $('.jobposition .menu .item').addClass('hide disabled');
          $('.jobposition .text').text('');
          $('input[name="jobposition"]').val('');
          $('.jobposition .menu .item').each(function() {
            var dept = $(this).attr('data-dept');
            if (dept == value) { $(this).removeClass('hide disabled'); }
          });
        }
      });
    });
  }
})();

