@extends('layouts.admin_v2') {{-- or your public layout --}}

@section('content')
<div class="auth-basic-wrapper d-flex align-items-center justify-content-center">
  <div class="container-fluid my-5 my-lg-0">
    <div class="row">
      <div class="col-12 col-md-8 col-lg-6 col-xl-5 col-xxl-4 mx-auto">
        <div class="card rounded-4 mb-0 border-top border-4 border-primary border-gradient-1">
          <div class="card-body p-5">

            <img src="{{ asset('assets2/images/logo1.png') }}" class="mb-4" width="145" alt="">

            <h3 class="mb-3">Submit Your Devotion</h3>

           

            <form action="{{ route('devotion.public.store') }}" method="POST" novalidate>
              @csrf

              {{-- honeypot (hidden anti-bot field) --}}
              <div style="position:absolute; left:-10000px; top:auto; width:1px; height:1px; overflow:hidden;">
                <label>Leave this field empty</label>
                <input type="text" name="hp" tabindex="-1" autocomplete="off">
              </div>

              <div class="mb-3">
                <label for="idno" class="form-label">ID Number</label>
                <input type="text" name="idno" id="idno" value="{{ old('idno') }}" class="form-control" required>
              </div>

              <div class="mb-3">
                <label for="devotion_date" class="form-label">Date</label>
                <input type="date" name="devotion_date" id="devotion_date" value="{{ old('devotion_date') }}" class="form-control" required>
              </div>

              <div class="mb-3">
                <label for="devotion_text" class="form-label">Devotion Text</label>
                <textarea name="devotion_text" id="devotion_text" rows="8" class="form-control" required>{{ old('devotion_text') }}</textarea>
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-primary">Submit Devotion</button>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection



<script type="text/javascript">
        $('.airdatepicker').datepicker({ language: 'en', dateFormat: 'yyyy-mm-dd' });
        $('.ui.dropdown.ministry').dropdown({ onChange: function(value, text, $selectedItem) {
            $('.jobposition .menu .item').addClass('hide');
            $('.jobposition .text').text('');
            $('input[name="jobposition"]').val('');
            $('.jobposition .menu .item').each(function() {
                var dept = $(this).attr('data-dept');
                if(dept == value) {$(this).removeClass('hide');};
            });
        }});

        function validateFile() {
            var f = document.getElementById("imagefile").value;
            var d = f.lastIndexOf(".") + 1;
            var ext = f.substr(d, f.length).toLowerCase();
            if (ext == "jpg" || ext == "jpeg" || ext == "png") { } else {
                document.getElementById("imagefile").value="";
                $.notify({
                icon: 'ui icon times',
                message: "Please upload only jpg/jpeg and png image formats."},
                {type: 'danger',timer: 400});
            }
        }

        var selected = "@isset($campus_details->leaveprivilege){{ $campus_details->leaveprivilege }}@endisset";
        var items = selected.split(',');
        $('.ui.dropdown.multiple.leaves').dropdown('set selected', items);
    </script>

</body>