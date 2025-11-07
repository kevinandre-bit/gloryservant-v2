// Report Devotions page scripts (CSP-friendly, no inline)
(function(){
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

  // Helpers
  function $(id){ return document.getElementById(id); }
  function pct(num, den){ if (!den || den <= 0) return 0; var v = (Number(num)/Number(den))*100; if (!isFinite(v)) return 0; return Math.min(100, Math.max(0, v)); }

  async function fetchDevotionData(){
    const campus     = $('filtercampus')    ? $('filtercampus').value    : '';
    const ministry   = $('filterministry')  ? $('filterministry').value  : '';
    const department = $('filterdepartment')? $('filterdepartment').value: '';
    const person     = $('filterperson')    ? $('filterperson').value    : '';
    const start      = $('startDate')       ? $('startDate').value       : '';
    const end        = $('endDate')         ? $('endDate').value         : '';

    if (!start || !end) { alert('Please select both Start Date and End Date.'); return; }

    let json;
    try {
      const qs = `?campus=${encodeURIComponent(campus||'')}`
        + `&ministry=${encodeURIComponent(ministry||'')}`
        + `&department=${encodeURIComponent(department||'')}`
        + `&person=${encodeURIComponent(person||'')}`
        + `&start_date=${encodeURIComponent(start)}`
        + `&end_date=${encodeURIComponent(end)}`;
      const res = await fetch(`/admin/reports/devotions/data${qs}`);
      if (!res.ok) throw new Error(`Server returned ${res.status}`);
      json = await res.json();
    } catch (err) {
      console.error('Fetch error:', err);
      if ($('filteredList')) $('filteredList').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>';
      return;
    }

    const data = Array.isArray(json.data) ? json.data : [];
    const totalEmployees = Number(json.total_employees) || 0;
    const days = Math.max(1, Number(json.days) || 1);

    // 1) Sort by percentage desc
    data.sort((a,b) => Number(b.percentage) - Number(a.percentage));

    // 2) Populate table safely
    const tbody = $('filteredList');
    if (tbody) {
      tbody.innerHTML = '';
      if (!data.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No data available</td></tr>';
      } else {
        data.forEach(row => {
          const fullName = `${row.firstname || ''} ${row.lastname || ''}`.trim() || '—';
          const dept     = row.department || row.ministry || '—';
          const camp     = row.campus || '—';
          const total    = Number(row.total) || 0;
          const percv    = Number(row.percentage) || 0;
          const tr = document.createElement('tr');
          const tdName = document.createElement('td'); tdName.textContent = fullName;
          const tdDept = document.createElement('td'); tdDept.textContent = dept;
          const tdCamp = document.createElement('td'); tdCamp.textContent = camp;
          const tdTotal= document.createElement('td'); tdTotal.className = 'text-end'; tdTotal.textContent = total.toLocaleString();
          const tdPerc = document.createElement('td'); tdPerc.className  = 'text-end'; tdPerc.textContent  = `${percv.toFixed(2)}%`;
          tr.appendChild(tdName); tr.appendChild(tdDept); tr.appendChild(tdCamp); tr.appendChild(tdTotal); tr.appendChild(tdPerc);
          tbody.appendChild(tr);
        });
      }
    }

    // 3) Overall (posts-based)
    const totalPosts    = data.reduce((sum, r) => sum + (Number(r.total) || 0), 0);
    const expectedPosts = totalEmployees * days;
    const achievedPct   = pct(totalPosts, Math.max(1, expectedPosts));
    const leftToGoal    = Math.max(0, expectedPosts - totalPosts);
    if ($('totalDevotionsSummary')) $('totalDevotionsSummary').textContent = totalPosts.toLocaleString();
    if ($('progressPercent'))       $('progressPercent').textContent       = `${achievedPct.toFixed(2)}%`;
    if ($('leftToGoalText'))        $('leftToGoalText').textContent        = `${leftToGoal.toLocaleString()} left to Goal`;
    if ($('devotionProgressBar'))   $('devotionProgressBar').style.width   = `${achievedPct}%`;
    if ($('devotionGrowthBadge'))   $('devotionGrowthBadge').textContent   = `${achievedPct.toFixed(2)}%`;

    // 4) No Devotion (people-based)
    const zeroCount   = data.filter(r => (Number(r.total) || 0) === 0).length;
    const zeroPct     = pct(zeroCount, Math.max(1, totalEmployees));
    const postedCount = Math.max(0, totalEmployees - zeroCount);
    const postedPct   = pct(postedCount, Math.max(1, totalEmployees));
    if ($('noDevotionCount'))             $('noDevotionCount').textContent             = zeroCount.toLocaleString();
    if ($('noDevotionBadge'))             $('noDevotionBadge').textContent             = `${zeroPct.toFixed(2)}%`;
    if ($('noDevotionLeftToGoalText'))    $('noDevotionLeftToGoalText').textContent    = `${postedCount.toLocaleString()} left to Goal`;
    if ($('noDevotionProgressPercent'))   $('noDevotionProgressPercent').textContent   = `${postedPct.toFixed(2)}%`;
    if ($('noDevotionProgressBar'))       $('noDevotionProgressBar').style.width       = `${postedPct}%`;
    if ($('noDevotionTitle')) $('noDevotionTitle').textContent = zeroCount > 0 ? 'People With No Devotions Yet' : '🎉 Everyone Posted At Least Once';

    // 5) Weekly goal (>=6)
    const sixPlusCount = data.filter(r => (Number(r.total) || 0) >= 6).length;
    const sixPlusPct   = pct(sixPlusCount, Math.max(1, totalEmployees));
    const missingSix   = Math.max(0, totalEmployees - sixPlusCount);
    if ($('sixPlusCount'))             $('sixPlusCount').textContent             = sixPlusCount.toLocaleString();
    if ($('sixPlusBadge'))             $('sixPlusBadge').textContent             = `${sixPlusPct.toFixed(2)}%`;
    if ($('sixPlusLeftToGoalText'))    $('sixPlusLeftToGoalText').textContent    = `${missingSix.toLocaleString()} left to Goal`;
    if ($('sixPlusProgressPercent'))   $('sixPlusProgressPercent').textContent   = `${sixPlusPct.toFixed(2)}%`;
    if ($('sixPlusProgressBar'))       $('sixPlusProgressBar').style.width       = `${sixPlusPct}%`;
    if ($('sixPlusTitle')) $('sixPlusTitle').textContent = sixPlusCount > 0 ? 'People Reached the Weekly Goal (6+ Devotions)' : 'No One Has Reached the Weekly Goal Yet';
  }

  document.addEventListener('DOMContentLoaded', () => {
    const startEl = $('startDate');
    const endEl   = $('endDate');
    // default last Saturday → Friday
    const today = new Date();
    const dow = today.getDay();
    const offsetToSaturday = (dow === 6) ? 0 : (dow + 1);
    const saturday = new Date(today); saturday.setDate(today.getDate() - offsetToSaturday);
    const friday = new Date(saturday); friday.setDate(saturday.getDate() + 6);
    const pad = n => String(n).padStart(2,'0'); const iso = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
    if (startEl && !startEl.value) startEl.value = iso(saturday);
    if (endEl && !endEl.value)     endEl.value   = iso(friday);

    ['filtercampus','filterministry','filterdepartment','filterperson','startDate','endDate'].forEach(id => {
      const el = $(id); if (el) el.addEventListener('change', fetchDevotionData);
    });
    fetchDevotionData();
  });
})();
