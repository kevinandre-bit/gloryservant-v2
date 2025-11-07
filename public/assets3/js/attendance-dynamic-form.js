// Attendance dynamic form: suggestions, autofill, UX
(function(){
  function byId(id){ return document.getElementById(id); }
  function readConfig(){
    var el = byId('attendance-config');
    if (!el) return {};
    try { return JSON.parse(el.textContent || '{}') || {}; } catch(e){ return {}; }
  }
  var CFG = readConfig();

  document.addEventListener('DOMContentLoaded', function(){
    var emailInput = byId('attEmail');
    var suggestionBox = byId('emailSuggest');
    var matchAlert = byId('matchAlert');
    var matchNameEl = byId('matchName');
    var guestFields = byId('progressiveFields');
    var editBtn = byId('editFields');
    var submitBtn = byId('submitBtn');
    if (!emailInput || !submitBtn) return;

    var fEmp = byId('attEmployee');
    var fCampus = byId('attCampus');
    var fMinistry = byId('attMinistry');
    var fDept = byId('attDept');

    var suggestions = [];
    var activeIndex = -1;
    var hasExact = false;

    var isValidEmail = function(value){ return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value); };
    var escHtml = function(text){ text = String(text||''); return text.replace(/[&<>"']/g, function(m){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[m]||m); }); };
    var hilite = function(text, query){ if(!query) return escHtml(text); var re=new RegExp('(' + query.replace(/[.*+?^${}()|[\\]\\]/g, '\\$&') + ')','ig'); return escHtml(text).replace(re,'<mark>$1</mark>'); };
    var getInitial = function(rec){ var s=(rec.employee||rec.email||'').trim(); return s ? s.charAt(0).toUpperCase() : '?'; };
    var selectByText = function(selectEl, valueText){ if(!selectEl) return; var t=String(valueText||'').trim().toUpperCase(); var matched=false; Array.from(selectEl.options).forEach(function(opt){ if (opt.value.trim().toUpperCase()===t){ opt.selected=true; matched=true; } }); if(!matched) selectEl.value=''; };
    var autofillProfile = function(profile){ emailInput.value = profile.email || emailInput.value; if (fEmp && profile.employee) fEmp.value = String(profile.employee).toUpperCase(); selectByText(fCampus, profile.campus||''); selectByText(fMinistry, profile.ministry||''); selectByText(fDept, profile.dept||''); };
    var setLocked = function(locked){ [fEmp,fCampus,fMinistry,fDept].forEach(function(el){ if(!el) return; if (el.tagName==='SELECT'){ el.disabled=locked; } else { el.readOnly=locked; } if (el.tagName!=='SELECT'){ el.classList.toggle('form-control-plaintext', locked); } }); };
    var setGuestVisible = function(show){ if(!guestFields) return; guestFields.classList.toggle('is-visible', show); guestFields.setAttribute('aria-hidden', show ? 'false' : 'true'); };
    var hideMatchAlert = function(){ if (matchAlert) matchAlert.classList.add('d-none'); };
    var showMatchAlert = function(name){ if(!matchAlert) return; if (matchNameEl) matchNameEl.textContent = name || 'Profile matched'; matchAlert.classList.remove('d-none'); };
    var closeSuggestions = function(){ if (suggestionBox){ suggestionBox.classList.add('d-none'); suggestionBox.innerHTML=''; } suggestions=[]; activeIndex=-1; };
    var renderSuggestions = function(query){ if (!suggestionBox) return; if (!suggestions.length){ closeSuggestions(); return; } var html=suggestions.map(function(item,idx){ var metaParts=[item.campus,item.ministry,item.dept].filter(Boolean); return '\n<button type="button" class="suggestion-card '+(idx===activeIndex?'active':'')+'" data-index="'+idx+'">\n  <div class="suggestion-avatar">'+escHtml(getInitial(item))+'</div>\n  <div class="suggestion-body">\n    <strong>'+hilite(item.employee||item.email, query)+'</strong>\n    <div class="suggestion-meta">'+hilite(item.email, query)+'</div>\n    '+(metaParts.length?('<div class="suggestion-meta">'+metaParts.map(escHtml).join(' • ')+'</div>'):'')+'\n  </div>\n</button>'; }).join(''); suggestionBox.innerHTML=html; suggestionBox.classList.remove('d-none'); };
    var setSubmitState = function(){ var emailOk=isValidEmail(emailInput.value.trim()); if (hasExact && emailOk){ submitBtn.disabled=false; return; } var nameOk = (fEmp && (fEmp.value||'').trim().length>1); submitBtn.disabled = !(emailOk && nameOk); };

    var suggestUrl = CFG.suggestUrl || '';
    var probeExact = async function(specificEmail){ var value=String(specificEmail || emailInput.value || '').trim().toLowerCase(); if (!isValidEmail(value)){ setSubmitState(); return; } try { var url=suggestUrl + (suggestUrl.indexOf('?')>-1?'&':'?') + 'email=' + encodeURIComponent(value); var res= await fetch(url, { headers: { 'X-Requested-With':'XMLHttpRequest' }, credentials: 'same-origin' }); var data= await res.json(); if (data.match && data.profile){ hasExact=true; setLocked(true); setGuestVisible(false); autofillProfile(data.profile); showMatchAlert(data.profile.employee || data.profile.email); } else { hasExact=false; setLocked(false); hideMatchAlert(); setGuestVisible(true); } closeSuggestions(); setSubmitState(); } catch(e){ console.error(e); } };
    var debounce = function(fn,delay){ var t; return function(){ var args=arguments, ctx=this; clearTimeout(t); t=setTimeout(function(){ fn.apply(ctx,args); }, delay); }; };
    var querySuggest = debounce(async function(){ var query=(emailInput.value||'').trim().toLowerCase(); if (!query){ hasExact=false; hideMatchAlert(); setGuestVisible(false); setLocked(false); closeSuggestions(); setSubmitState(); return; } try { var url=suggestUrl + (suggestUrl.indexOf('?')>-1?'&':'?') + 'email=' + encodeURIComponent(query); var res= await fetch(url, { headers:{'X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin' }); var data= await res.json(); if (data.match && data.profile){ hasExact=true; setLocked(true); setGuestVisible(false); autofillProfile(data.profile); showMatchAlert(data.profile.employee || data.profile.email); closeSuggestions(); } else { hasExact=false; hideMatchAlert(); setLocked(false); setGuestVisible(false); suggestions = Array.isArray(data.suggestions)? data.suggestions: []; activeIndex=-1; renderSuggestions(query); } setSubmitState(); } catch(e){ console.error(e); } }, 200);

    emailInput.addEventListener('input', querySuggest);
    emailInput.addEventListener('focus', querySuggest);
    emailInput.addEventListener('blur', function(){ setTimeout(function(){ if (!suggestionBox || suggestionBox.classList.contains('d-none')) probeExact(); }, 150); });
    emailInput.addEventListener('keydown', function(event){ if (!suggestionBox || suggestionBox.classList.contains('d-none')) return; if (event.key==='ArrowDown'){ event.preventDefault(); activeIndex=(activeIndex+1)%suggestions.length; renderSuggestions(emailInput.value.trim()); } else if (event.key==='ArrowUp'){ event.preventDefault(); activeIndex=(activeIndex-1+suggestions.length)%suggestions.length; renderSuggestions(emailInput.value.trim()); } else if (event.key==='Enter'){ if (activeIndex>-1){ event.preventDefault(); probeExact((suggestions[activeIndex]||{}).email||null); } } else if (event.key==='Escape'){ closeSuggestions(); } });
    suggestionBox && suggestionBox.addEventListener('click', function(e){ var btn=e.target.closest('[data-index]'); if (!btn) return; var rec=suggestions[Number(btn.dataset.index)]; if (rec) probeExact(rec.email); });
    editBtn && editBtn.addEventListener('click', function(){ hideMatchAlert(); hasExact=false; setLocked(false); setGuestVisible(true); setSubmitState(); });
    fEmp && fEmp.addEventListener('input', setSubmitState);

    var pre = typeof CFG.prefillEmail === 'string' && CFG.prefillEmail.trim() ? CFG.prefillEmail.trim().toLowerCase() : null;
    if (pre) { probeExact(pre); } else { setSubmitState(); }
  });

  // Minimal UX: button loading state
  document.addEventListener('DOMContentLoaded', function(){ var form=byId('attendanceForm'); var btn=byId('submitBtn'); var spn=byId('btnSpinner'); if (form && btn && spn){ form.addEventListener('submit', function(){ btn.setAttribute('disabled','disabled'); spn.classList.remove('d-none'); }); } });
})();

