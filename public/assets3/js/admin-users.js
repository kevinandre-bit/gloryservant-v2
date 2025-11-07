// Users page scripts
(function(){
  // Table search filter
  document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('tableSearch');
    const rows = document.querySelectorAll('#userTableBody tr');
    if (searchInput && rows.length) {
      searchInput.addEventListener('keyup', function () {
        const value = this.value.toLowerCase();
        rows.forEach(row => {
          row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';
        });
      });
    }
  });

  // Add User modal autofill
  document.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('selEmployee');
    const email = document.getElementById('txtEmail');
    const ref = document.getElementById('hdnRef');

    sel?.addEventListener('change', () => {
      const opt = sel.selectedOptions[0];
      if (!opt) return;
      email.value = opt.getAttribute('data-email') || '';
      ref.value   = opt.getAttribute('data-ref')   || '';
    });

    const modalEl = document.getElementById('addUserModal');
    modalEl?.addEventListener('hidden.bs.modal', () => {
      if (sel) sel.value = '';
      if (email) email.value = '';
      if (ref) ref.value = '';
      document.getElementById('add_user_form')?.reset();
    });
  });

  // Permissions Modal logic + category-level scroll
  document.addEventListener('DOMContentLoaded', () => {
    const modalEl = document.getElementById('userPermissionsModal');
    if (!modalEl) return;

    const shell = modalEl.querySelector('#userPermissionsShell');
    const sidebar = modalEl.querySelector('#userPermissionsSidebar');
    const categoryNav = modalEl.querySelector('#userPermissionsCategoryNav');
    const bodyEl = modalEl.querySelector('#userPermissionsBody');
    const emptyEl = modalEl.querySelector('#userPermissionsEmpty');
    const alertEl = modalEl.querySelector('#userPermissionsAlert');
    const nameEl = modalEl.querySelector('[data-user-name]');
    const formEl = modalEl.querySelector('form');

    function sizeCategoryScroll() {
      if (!sidebar) return;
      const h = sidebar.clientHeight || 0;
      const header = sidebar.querySelector('.sidebar-title');
      const headerH = header ? header.clientHeight : 0;
      const nav = sidebar.querySelector('.nav');
      if (!nav) return;
      const max = Math.max(120, h - headerH - 16);
      nav.style.setProperty('--cat-items-max', max + 'px');
      const itemsBlocks = sidebar.querySelectorAll('.category-items');
      itemsBlocks.forEach(b => b.style.maxHeight = max + 'px');
    }

    const categoryMap = new Map([
      ['Account', 'Account'],
      ['Attendance', 'Attendance'],
      ['Schedule', 'Schedules'],
      ['Reports', 'Reports'],
      ['Admin', 'Admin'],
    ]);
    function deriveSection(label) {
      for (const [k,v] of categoryMap) {
        if ((label||'').toLowerCase().includes(k.toLowerCase())) return v;
      }
      return 'Other';
    }

    function renderGroups(groups) {
      if (!Array.isArray(groups)) groups = [];
      sidebar.classList.remove('d-none');
      emptyEl.textContent = '';
      categoryNav.innerHTML = '';
      bodyEl.innerHTML = '';

      const sections = [];
      const renderRow = (item) => {
        const id = String(item.id ?? '');
        const label = String(item.label ?? '');
        const name = String(item.name ?? '');
        const checked = !!item.allowed;
        return `
          <div class="permission-row">
            <div class="permission-label">${label}</div>
            <div class="permission-meta">${name}</div>
            <div class="segmented btn-group" role="group">
              <input type="radio" class="btn-check" name="perm[${id}]" id="perm-${id}-allow" value="1" ${checked?'checked':''}>
              <label class="btn btn-sm btn-outline-success" for="perm-${id}-allow">Allow</label>
              <input type="radio" class="btn-check" name="perm[${id}]" id="perm-${id}-deny" value="0" ${!checked?'checked':''}>
              <label class="btn btn-sm btn-outline-danger" for="perm-${id}-deny">Deny</label>
            </div>
          </div>`;
      };

      groups.forEach((group, index) => {
        const safeId = `perm-group-${index}`;
        const isActive = index === 0;

        bodyEl.insertAdjacentHTML('beforeend', `
          <div class="permissions-pane ${isActive ? 'active' : ''}" id="${safeId}">
            ${(group.items || []).map(renderRow).join('') || '<p class="text-muted">No permissions in this category.</p>'}
          </div>
        `);

        const sectionLabel = deriveSection(group.group);
        let section = sections.find(s => s.label === sectionLabel);
        if (!section) {
          const order = categoryMap.has(sectionLabel)
            ? Array.from(categoryMap.keys()).indexOf(sectionLabel)
            : categoryMap.size;
          section = { label: sectionLabel, items: [], order };
          sections.push(section);
        }
        section.items.push({ label: group.group, safeId, active: isActive });
      });

      sections.sort((a, b) => a.order - b.order);

      categoryNav.innerHTML = sections.map((section, sectionIdx) => {
        const expanded = sectionIdx === 0 ? 'expanded' : '';
        return `
          <div class="category-section ${expanded}">
            <button type="button" class="section-toggle" data-section="${sectionIdx}">
              <span>${section.label}</span>
              <span class="chevron"></span>
            </button>
            <div class="category-items ${sectionIdx === 0 ? 'show' : ''}">
              ${section.items.map(item => `
                <button type="button" class="nav-link text-start ${item.active ? 'active' : ''}" data-target="#${item.safeId}">
                  ${item.label}
                </button>
              `).join('')}
            </div>
          </div>
        `;
      }).join('');

      categoryNav.querySelectorAll('.section-toggle').forEach(toggle => {
        toggle.addEventListener('click', () => {
          const sectionEl = toggle.closest('.category-section');
          const itemsEl = sectionEl.querySelector('.category-items');
          const expanded = itemsEl.classList.toggle('show');
          sectionEl.classList.toggle('expanded', expanded);
          sizeCategoryScroll();
        });
      });

      categoryNav.querySelectorAll('.category-items .nav-link').forEach(btn => {
        btn.addEventListener('click', () => {
          categoryNav.querySelectorAll('.category-items .nav-link').forEach(b => b.classList.remove('active'));
          btn.classList.add('active');

          bodyEl.querySelectorAll('.permissions-pane').forEach(pane => pane.classList.remove('active'));
          const target = bodyEl.querySelector(btn.dataset.target);
          if (target) target.classList.add('active');
        });
      });

      sizeCategoryScroll();
    }

    document.querySelectorAll('.btn-user-permissions').forEach(button => {
      button.addEventListener('click', async () => {
        const jsonUrl = button.dataset.permissionsUrl;
        const updateUrl = button.dataset.updateUrl;

        formEl.action = updateUrl;
        nameEl.textContent = button.dataset.userName || '';
        alertEl.classList.add('d-none');
        alertEl.textContent = '';
        sidebar.classList.add('d-none');
        categoryNav.innerHTML = '';
        bodyEl.innerHTML = '';
        emptyEl.textContent = '';
        emptyEl.insertAdjacentHTML('beforeend', '<div class="text-center w-100 py-5"><div class="spinner-border text-primary" role="status"></div></div>');

        try {
          const response = await fetch(jsonUrl, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
          if (!response.ok) throw new Error(`HTTP ${response.status}`);

          const data = await response.json();
          nameEl.textContent = data?.user?.name ?? button.dataset.userName ?? 'User';
          const scopeSelect = modalEl.querySelector('#userScopeLevel');
          if (scopeSelect && data?.user?.scope_level) {
            scopeSelect.value = data.user.scope_level;
          }
          renderGroups(data.permissions || []);
        } catch (error) {
          alertEl.textContent = `Unable to load permissions for this user. (${error.message ?? error})`;
          alertEl.classList.remove('d-none');
          sidebar.classList.add('d-none');
          categoryNav.innerHTML = '';
          bodyEl.innerHTML = '';
          emptyEl.textContent = 'No permission data available.';
        }
      });
    });

    modalEl.addEventListener('shown.bs.modal', () => setTimeout(sizeCategoryScroll, 50));
    window.addEventListener('resize', sizeCategoryScroll);

    modalEl.addEventListener('hidden.bs.modal', () => {
      sidebar.classList.add('d-none');
      categoryNav.innerHTML = '';
      bodyEl.innerHTML = '';
      emptyEl.textContent = '';
      alertEl.classList.add('d-none');
      alertEl.textContent = '';
      nameEl.textContent = '';
    });
  });

  // feather icons refresh if present
  if (window.feather && typeof window.feather.replace === 'function') {
    window.feather.replace();
  }

  // Edit modal populate
  document.addEventListener('DOMContentLoaded', function () {
    var formModal = document.getElementById('FormModal');
    if (!formModal) return;

    formModal.addEventListener('shown.bs.modal', function (event) {
      var btn   = event.relatedTarget;
      var modal = this;

      var roleId = btn.getAttribute('data-roleid');
      var dest   = modal.querySelector('#modalRoleId');

      // Safely populate select without using innerHTML
      var roles = (function(){
        if (Array.isArray(window.ROLES)) return window.ROLES;
        var jsonEl = document.getElementById('roles-json');
        if (jsonEl) { try { var p = JSON.parse(jsonEl.textContent || '[]'); if (Array.isArray(p)) return p; } catch(e) {} }
        return [];
      })();
      while (dest.firstChild) dest.removeChild(dest.firstChild);
      var opt0 = document.createElement('option');
      opt0.value = '';
      opt0.textContent = 'Select Role';
      dest.appendChild(opt0);
      roles.forEach(function (r) {
        var o = document.createElement('option');
        o.value = String(r.id);
        o.textContent = r.role_name || ('Role ' + r.id);
        dest.appendChild(o);
      });

      var valueToSet = roleId != null ? String(roleId) : '';
      dest.value = valueToSet;
      dest.dispatchEvent(new Event('change', { bubbles: true }));

      modal.querySelector('#modalName').value  = btn.getAttribute('data-name')  || '';
      modal.querySelector('#modalEmail').value = btn.getAttribute('data-email') || '';
      modal.querySelector('#modalReference').value = btn.getAttribute('data-id')    || '';
      modal.querySelector('#modalIdno').value      = btn.getAttribute('data-idno')  || '';
      modal.querySelector('#modalWorkType').value  = btn.getAttribute('data-worktype') || '';

      var accType = String(btn.getAttribute('data-acc') || '');
      modal.querySelector('#modalAccVolunteer').checked = (accType === '1');
      modal.querySelector('#modalAccAdmin').checked     = (accType === '2');

      var statusSelect = modal.querySelector('#modalStatus');
      var s = String(btn.getAttribute('data-status') || '');
      if (statusSelect) statusSelect.value = s;

      var scopeSelect = modal.querySelector('#modalScopeLevel');
      var scope = String(btn.getAttribute('data-scopelevel') || 'inherit');
      if (scopeSelect) scopeSelect.value = scope;
    });

    formModal.addEventListener('hidden.bs.modal', function () {
      const f = formModal.querySelector('#edit_user_form');
      if (f) f.reset();
    });
  });

  // Work-type update AJAX + toast fallback
  document.addEventListener('DOMContentLoaded', function () {
    function fallbackAlert(msg, type = 'success', ms = 3000) {
      const existing = document.getElementById('workTypeAlert');
      if (!existing) {
        const el = document.createElement('div');
        el.id = 'workTypeAlert';
        el.className = 'alert alert-' + (type === 'error' ? 'danger' : type) + ' position-fixed top-0 end-0 m-3';
        el.style.zIndex = 9999;
        el.textContent = msg;
        document.body.appendChild(el);
        setTimeout(()=> el.remove(), ms);
      } else {
        existing.className = 'alert alert-' + (type === 'error' ? 'danger' : type) + ' position-fixed top-0 end-0 m-3';
        existing.textContent = msg;
        existing.classList.remove('d-none');
        setTimeout(()=> existing.classList.add('d-none'), ms);
      }
    }

    function showNotify(msg, type = 'success') {
      if (window.Lobibox && typeof Lobibox.notify === 'function') {
        Lobibox.notify(type, {
          pauseDelayOnHover: true,
          continueDelayOnInactiveTab: false,
          position: 'top right',
          icon: type === 'success' ? 'bi bi-check2-circle' : 'bi bi-x-circle',
          msg: msg
        });
      } else {
        fallbackAlert(msg, type === 'error' ? 'error' : 'success');
      }
    }

    document.querySelectorAll('.update-work-type').forEach(select => {
      select.addEventListener('change', function () {
        const reference = this.dataset.reference || this.dataset.id;
        const workType  = this.value;

        const payload = new URLSearchParams();
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const token = csrfMeta ? csrfMeta.getAttribute('content') : '';
        payload.append('_token', token);
        payload.append('id', reference);
        payload.append('work_type', workType);

        fetch('/users/update-worktype', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: payload.toString(),
          credentials: 'same-origin'
        })
        .then(async res => {
          const ct = res.headers.get('content-type') || '';
          let json = {};
          if (ct.includes('application/json')) json = await res.json();

          if (!res.ok && res.status === 422) {
            const msg = (json && json.errors) ? Object.values(json.errors).flat().join('; ') : (json.message || 'Validation error');
            showNotify(msg, 'error');
            return;
          }

          if (json && json.success) {
            showNotify(json.message || 'Work type updated', 'success');
          } else {
            showNotify(json.message || 'No rows updated', 'error');
          }
        })
        .catch(err => {
          console.error('update-worktype error', err);
          showNotify('Network or server error', 'error');
        });
      });
    });
  });

  // DataTables initialisation (if present)
  if (window.jQuery) {
    jQuery(function($){
      if ($('#example').length) { $('#example').DataTable(); }
      if ($('#example2').length) {
        var table = $('#example2').DataTable({
          pageLength: 15,
          lengthChange: false,
          buttons: [ 'copy', 'excel', 'pdf', 'print' ]
        });
        if (table && table.buttons) {
          table.buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
        }
      }
    });
  }
})();
