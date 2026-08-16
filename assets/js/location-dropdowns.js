/**
 * Wires up State -> District -> Taluka dependent dropdowns.
 * Usage: give the three <select> elements matching data attributes and call
 * initLocationDropdowns() once the DOM is ready. Works for any number of
 * state/district/taluka trios on the same page (e.g. a filter row + a form).
 *
 *   <select data-location="state"></select>
 *   <select data-location="district" disabled></select>
 *   <select data-location="taluka" disabled></select>
 *
 * Each select's containing element (`data-location-group`) is optional; if
 * present, dropdowns are looked up within that scope instead of globally,
 * so multiple independent trios can coexist on one page.
 */
(function () {
  const API_BASE = (window.NGO_BASE_URL || '') + '/api/index.php';

  function placeholder(select, text) {
    select.innerHTML = '';
    const opt = document.createElement('option');
    opt.value = '';
    opt.textContent = text;
    select.appendChild(opt);
  }

  function fillOptions(select, rows, emptyText) {
    placeholder(select, rows.length ? 'Select' : emptyText);
    rows.forEach((row) => {
      const opt = document.createElement('option');
      opt.value = row.id;
      opt.textContent = row.name;
      select.appendChild(opt);
    });
    const otherOpt = document.createElement('option');
    otherOpt.value = 'other';
    otherOpt.textContent = 'Other';
    select.appendChild(otherOpt);
  }

  async function fetchJson(url) {
    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    if (!res.ok) throw new Error('Request failed');
    return res.json();
  }

  function wireGroup(scope) {
    const stateSel = scope.querySelector('[data-location="state"]');
    const districtSel = scope.querySelector('[data-location="district"]');
    const talukaSel = scope.querySelector('[data-location="taluka"]');
    if (!stateSel || !districtSel || !talukaSel) return;

    const initialDistrictId = districtSel.getAttribute('data-selected') || '';
    const initialTalukaId = talukaSel.getAttribute('data-selected') || '';

    function resetDistrict() {
      placeholder(districtSel, 'Select State First');
      districtSel.disabled = true;
    }
    function resetTaluka() {
      placeholder(talukaSel, 'Select District First');
      talukaSel.disabled = true;
    }

    async function loadDistricts(stateId, preselect) {
      if (!stateId) { resetDistrict(); resetTaluka(); return; }
      districtSel.disabled = true;
      placeholder(districtSel, 'Loading...');
      try {
        const data = await fetchJson(`${API_BASE}?resource=districts&state_id=${encodeURIComponent(stateId)}`);
        fillOptions(districtSel, data.districts || [], 'No districts found');
        districtSel.disabled = false;
        if (preselect) districtSel.value = preselect;
      } catch (e) {
        placeholder(districtSel, 'Could not load districts');
      }
    }

    async function loadTalukas(districtId, preselect) {
      if (!districtId || districtId === 'other') { resetTaluka(); return; }
      talukaSel.disabled = true;
      placeholder(talukaSel, 'Loading...');
      try {
        const data = await fetchJson(`${API_BASE}?resource=talukas&district_id=${encodeURIComponent(districtId)}`);
        fillOptions(talukaSel, data.talukas || [], 'No talukas found - select Other');
        talukaSel.disabled = false;
        if (preselect) talukaSel.value = preselect;
      } catch (e) {
        placeholder(talukaSel, 'Could not load talukas');
      }
    }

    stateSel.addEventListener('change', () => loadDistricts(stateSel.value));
    districtSel.addEventListener('change', () => loadTalukas(districtSel.value));

    if (stateSel.value) {
      loadDistricts(stateSel.value, initialDistrictId).then(() => {
        if (initialDistrictId && initialDistrictId !== 'other') {
          loadTalukas(initialDistrictId, initialTalukaId);
        }
      });
    } else {
      resetDistrict();
      resetTaluka();
    }
  }

  window.initLocationDropdowns = function (root) {
    const scopes = (root || document).querySelectorAll('[data-location-group]');
    if (scopes.length) {
      scopes.forEach(wireGroup);
    } else {
      wireGroup(root || document);
    }
  };

  document.addEventListener('DOMContentLoaded', () => window.initLocationDropdowns());
})();
