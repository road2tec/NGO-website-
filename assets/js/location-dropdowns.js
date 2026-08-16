/**
 * Wires up State -> District -> Taluka dependent dropdowns, each with an
 * "Other" fallback that reveals a free-text input. Works for any number of
 * trios on the same page (e.g. a filter row + a form) as long as each trio
 * is wrapped in an element carrying [data-location-group].
 *
 *   <div data-location-group>
 *     <select data-location="state" name="state_id"></select>
 *     <select data-location="district" name="district_id" disabled></select>
 *     <input data-other-for="district" name="district_other" class="d-none">
 *     <select data-location="taluka" name="taluka_id" disabled></select>
 *     <input data-other-for="taluka" name="taluka_other" class="d-none">
 *   </div>
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

  function showOther(input, show) {
    if (!input) return;
    input.classList.toggle('d-none', !show);
    input.required = show;
    input.disabled = !show;
    if (!show) input.value = '';
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

    const districtOther = scope.querySelector('[data-other-for="district"]');
    const talukaOther = scope.querySelector('[data-other-for="taluka"]');

    const initialDistrictId = districtSel.getAttribute('data-selected') || '';
    const initialTalukaId = talukaSel.getAttribute('data-selected') || '';

    function resetDistrict() {
      placeholder(districtSel, 'Select State First');
      districtSel.disabled = true;
      showOther(districtOther, false);
    }
    function resetTaluka(text) {
      placeholder(talukaSel, text || 'Select District First');
      talukaSel.disabled = true;
      showOther(talukaOther, false);
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
        handleDistrictChange();
      } catch (e) {
        placeholder(districtSel, 'Could not load districts');
      }
    }

    async function loadTalukas(districtId, preselect) {
      talukaSel.disabled = true;
      placeholder(talukaSel, 'Loading...');
      try {
        const data = await fetchJson(`${API_BASE}?resource=talukas&district_id=${encodeURIComponent(districtId)}`);
        fillOptions(talukaSel, data.talukas || [], 'No talukas found - select Other');
        talukaSel.disabled = false;
        if (preselect) {
          talukaSel.value = preselect;
          showOther(talukaOther, preselect === 'other');
        }
      } catch (e) {
        placeholder(talukaSel, 'Could not load talukas');
      }
    }

    // A district of "Other" means there is no known district to look talukas
    // up against, so the taluka becomes a free-text field too.
    function handleDistrictChange() {
      showOther(districtOther, districtSel.value === 'other');
      if (districtSel.value === 'other') {
        resetTaluka('Not applicable - enter below');
        showOther(talukaOther, true);
      } else if (districtSel.value) {
        loadTalukas(districtSel.value, initialTalukaId);
      } else {
        resetTaluka();
      }
    }

    stateSel.addEventListener('change', () => loadDistricts(stateSel.value));
    districtSel.addEventListener('change', handleDistrictChange);
    talukaSel.addEventListener('change', () => showOther(talukaOther, talukaSel.value === 'other'));

    if (stateSel.value) {
      loadDistricts(stateSel.value, initialDistrictId);
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
