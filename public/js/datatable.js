document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('filter-form');
    if (!filterForm) return;

    let debounceTimer;
    
    // Listen to all inputs and selects within the filter form
    const inputs = filterForm.querySelectorAll('input:not([type="hidden"]), select');
    inputs.forEach(input => {
        const eventType = input.tagName.toLowerCase() === 'select' ? 'change' : 'input';
        input.addEventListener(eventType, function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchTableData();
            }, 500);
        });
    });

    // Handle Sorting clicks
    document.addEventListener('click', function(e) {
        const th = e.target.closest('.sortable-column');
        if (th) {
            const field = th.dataset.field;
            const dir = th.dataset.dir;
            document.getElementById('sort_by_input').value = field;
            document.getElementById('sort_dir_input').value = dir;
            fetchTableData();
        }
        
        // Handle Pagination clicks
        const pageLink = e.target.closest('.pagination a');
        if (pageLink && pageLink.closest('#table-wrapper')) {
            e.preventDefault();
            const url = new URL(pageLink.href);
            fetchTableData(url);
        }
    });

    function fetchTableData(fetchUrl = null) {
        showLoader();
        
        // Reset export input just in case
        document.getElementById('export_input').value = '';
        
        const url = fetchUrl || new URL(filterForm.action);
        if (!fetchUrl) {
            const formData = new FormData(filterForm);
            for (let [key, value] of formData.entries()) {
                if (value) {
                    url.searchParams.set(key, value);
                }
            }
        }

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => response.text())
        .then(html => {
            const temp = document.createElement('div');
            temp.innerHTML = html;
            const newWrapper = temp.querySelector('#table-wrapper') || temp;
            
            const currentWrapper = document.getElementById('table-wrapper');
            if (currentWrapper && temp.querySelector('#table-wrapper')) {
                currentWrapper.replaceWith(newWrapper);
            } else if (currentWrapper) {
                currentWrapper.innerHTML = html;
            }
            
            // Update URL in browser without reloading
            window.history.pushState({}, '', url);
        })
        .catch(error => console.error('Error fetching data:', error))
        .finally(() => hideLoader());
    }
    
    function showLoader() {
        const loader = document.getElementById('table-loader');
        if (loader) loader.classList.remove('d-none');
        const cardBody = document.querySelector('#table-wrapper .card-body');
        if (cardBody) cardBody.style.opacity = '0.5';
    }

    function hideLoader() {
        const loader = document.getElementById('table-loader');
        if (loader) loader.classList.add('d-none');
        const cardBody = document.querySelector('#table-wrapper .card-body');
        if (cardBody) cardBody.style.opacity = '1';
    }
});

// Export handler
window.exportData = function(type) {
    const form = document.getElementById('filter-form');
    document.getElementById('export_input').value = type;
    form.submit();
    
    // Reset after submit
    setTimeout(() => {
        document.getElementById('export_input').value = '';
    }, 1000);
}
