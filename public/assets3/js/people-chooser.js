(function(){
  document.addEventListener('DOMContentLoaded', function () {
    var searchEl = document.getElementById('peopleSearch');
    var perPageEl= document.getElementById('perPage');
    var listEl   = document.getElementById('peopleList');
    var prevEl   = document.getElementById('prevPage');
    var nextEl   = document.getElementById('nextPage');
    var rangeEl  = document.getElementById('rangeInfo');
    var selCount = document.getElementById('selectedCount');
    var sinkEl   = document.getElementById('selectedSink');
    if (!listEl || !prevEl || !nextEl || !rangeEl || !sinkEl) return;

    var cfgEl = document.getElementById('people-config');
    var cfg = { people:[], preselected:[] };
    if (cfgEl) { try { cfg = JSON.parse(cfgEl.textContent || '{}') || cfg; } catch(e) {} }
    var PEOPLE = Array.isArray(cfg.people) ? cfg.people : [];
    var PRESEL = new Set(Array.isArray(cfg.preselected) ? cfg.preselected : []);

    var state = { term:'', page:1, perPage:(function(){ var v=parseInt(perPageEl && perPageEl.value,10); return Number.isFinite(v)&&v>0?v:20; })(), selected:new Set(PRESEL) };
    var norm = function(s){ return String(s||'').toLowerCase().trim(); };

    function filtered(){ if (!state.term) return PEOPLE; var t=norm(state.term); return PEOPLE.filter(function(p){ return norm(p.name).includes(t)||norm(p.campus).includes(t)||norm(p.ministry).includes(t); }); }
    function pageSlice(rows){ var start=(state.page-1)*state.perPage; return rows.slice(start, start+state.perPage); }
    function syncHidden(){ sinkEl.innerHTML=''; state.selected.forEach(function(id){ var input=document.createElement('input'); input.type='hidden'; input.name='person_ids[]'; input.value=id; sinkEl.appendChild(input); }); }
    function updateSelectedCount(){ if (selCount) selCount.textContent = state.selected.size + ' selected'; }

    function render(){
      var rows = filtered();
      var total = rows.length; var maxPg=Math.max(1, Math.ceil(total/state.perPage)); if (state.page>maxPg) state.page=maxPg;
      var slice = pageSlice(rows); listEl.innerHTML='';
      if (!slice.length) {
        var empty=document.createElement('div'); empty.className='p-3 text-muted'; empty.textContent = total ? 'No results on this page.' : 'No people found.'; listEl.appendChild(empty);
      } else {
        slice.forEach(function(p){
          var id=String(p.id); var campus=p.campus?('Campus: '+p.campus):''; var ministry=p.ministry?('Ministry: '+p.ministry):''; var sub=[campus,ministry].filter(Boolean).join(' — ');
          var row=document.createElement('div'); row.className='form-check border-bottom py-2 px-2 d-flex align-items-start gap-2';
          row.innerHTML = '<input class="form-check-input mt-1" type="checkbox" value="'+id+'" id="p-'+id+'">\n              <label class="form-check-label w-100" for="p-'+id+'">\n                <div class="fw-semibold">'+(p.name||'(no name)')+'</div>\n                '+(sub?('<div class="text-muted small">'+sub+'</div>'):'')+'\n              </label>';
          row.addEventListener('click', function(e){ var cb=row.querySelector('input[type="checkbox"]'); if (e.target!==cb) { cb.checked=!cb.checked; cb.dispatchEvent(new Event('change',{bubbles:true})); }});
          var cb=row.querySelector('input[type="checkbox"]'); cb.checked=state.selected.has(Number(id)); cb.addEventListener('change', function(e){ var pid=Number(e.target.value); if (e.target.checked) state.selected.add(pid); else state.selected.delete(pid); syncHidden(); updateSelectedCount(); });
          listEl.appendChild(row);
        });
      }
      var start= total? ((state.page-1)*state.perPage+1):0; var end=Math.min(state.page*state.perPage, total);
      rangeEl.textContent = start+'–'+end+' of '+total;
      prevEl.disabled = state.page<=1; nextEl.disabled = state.page>=maxPg;
      updateSelectedCount(); syncHidden();
    }

    var tId=null; function debounce(fn,delay){ return function(){ var args=arguments, ctx=this; clearTimeout(tId); tId=setTimeout(function(){ fn.apply(ctx,args); }, delay); } };
    if (searchEl) searchEl.addEventListener('input', debounce(function(e){ state.term = norm(e.target.value); state.page=1; render(); },120));
    if (perPageEl) perPageEl.addEventListener('change', function(e){ var v=parseInt(e.target.value,10); state.perPage = (Number.isFinite(v)&&v>0)?v:20; state.page=1; render(); });
    prevEl.addEventListener('click', function(){ if (state.page>1){ state.page--; render(); }});
    nextEl.addEventListener('click', function(){ state.page++; render(); });
    render();
  });
})();

