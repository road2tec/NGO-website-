/**
 * Wires up Job Category -> Subcategory dependent dropdowns. Works for any
 * number of pairs on the same page as long as each is wrapped in an element
 * carrying [data-job-category-group].
 *
 *   <div data-job-category-group>
 *     <select data-job="category" name="category_id"></select>
 *     <select data-job="subcategory" name="subcategory_id" disabled></select>
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
    placeholder(select, rows.length ? 'All subcategories' : emptyText);
    rows.forEach((row) => {
      const opt = document.createElement('option');
      opt.value = row.id;
      opt.textContent = row.name;
      select.appendChild(opt);
    });
  }

  async function fetchJson(url) {
    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    if (!res.ok) throw new Error('Request failed');
    return res.json();
  }

  function wireGroup(scope) {
    const categorySel = scope.querySelector('[data-job="category"]');
    const subcategorySel = scope.querySelector('[data-job="subcategory"]');
    if (!categorySel || !subcategorySel) return;

    const initialSubcategoryId = subcategorySel.getAttribute('data-selected') || '';

    async function loadSubcategories(categoryId, preselect) {
      if (!categoryId) {
        placeholder(subcategorySel, 'Select a category first');
        subcategorySel.disabled = true;
        return;
      }
      subcategorySel.disabled = true;
      placeholder(subcategorySel, 'Loading...');
      try {
        const data = await fetchJson(`${API_BASE}?resource=job_subcategories&category_id=${encodeURIComponent(categoryId)}`);
        fillOptions(subcategorySel, data.subcategories || [], 'No subcategories');
        subcategorySel.disabled = false;
        if (preselect) subcategorySel.value = preselect;
      } catch (e) {
        placeholder(subcategorySel, 'Could not load subcategories');
      }
    }

    categorySel.addEventListener('change', () => loadSubcategories(categorySel.value));

    if (categorySel.value) {
      loadSubcategories(categorySel.value, initialSubcategoryId);
    } else {
      placeholder(subcategorySel, 'Select a category first');
      subcategorySel.disabled = true;
    }
  }

  window.initJobCategoryDropdowns = function (root) {
    (root || document).querySelectorAll('[data-job-category-group]').forEach(wireGroup);
  };

  document.addEventListener('DOMContentLoaded', () => window.initJobCategoryDropdowns());
})();
