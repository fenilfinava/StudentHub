document.addEventListener('DOMContentLoaded', () => {
    let currentData = [];
    let filteredData = [];
    let currentType = 'students';
    let currentPage = 1;
    const itemsPerPage = 5;

    // DOM Elements
    const statusContainer = document.getElementById('status-container');
    const tableContainer = document.getElementById('data-table-container');
    const paginationContainer = document.getElementById('pagination-container');
    const searchInput = document.getElementById('searchInput');
    const filterSelect = document.getElementById('filterSelect');
    const sortSelect = document.getElementById('sortSelect');
    const tabBtns = document.querySelectorAll('.tab-btn');

    // Initialize
    fetchData(currentType);

    // Event Listeners
    tabBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            tabBtns.forEach(b => b.classList.remove('active'));
            e.target.classList.add('active');
            currentType = e.target.getAttribute('data-type');
            searchInput.value = '';
            sortSelect.value = 'default';
            fetchData(currentType);
        });
    });

    searchInput.addEventListener('input', applyFilters);
    filterSelect.addEventListener('change', applyFilters);
    sortSelect.addEventListener('change', applyFilters);

    // Fetch JSON Data
    async function fetchData(type) {
        showLoading();
        try {
            // Simulate network delay for demonstration
            await new Promise(resolve => setTimeout(resolve, 800));
            
            const response = await fetch(`data/${type}.json`);
            if (!response.ok) throw new Error('Failed to fetch data');
            
            const data = await response.json();
            currentData = data;
            
            populateFilterOptions(type);
            applyFilters();
        } catch (error) {
            showError(`Error loading data: ${error.message}`);
        }
    }

    // Populate Filter Dropdown dynamically based on data type
    function populateFilterOptions(type) {
        filterSelect.innerHTML = '<option value="all">All Categories/Branches</option>';
        let options = new Set();
        
        currentData.forEach(item => {
            if (type === 'students') options.add(item.branch);
            if (type === 'events') options.add(item.category);
        });

        options.forEach(opt => {
            if (opt) {
                filterSelect.innerHTML += `<option value="${opt}">${opt}</option>`;
            }
        });
    }

    // Filter, Search, and Sort Logic
    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase();
        const filterVal = filterSelect.value;
        const sortVal = sortSelect.value;

        filteredData = currentData.filter(item => {
            // Search logic (check values of object)
            const matchesSearch = Object.values(item).some(val => 
                String(val).toLowerCase().includes(searchTerm)
            );
            
            // Filter logic
            let matchesFilter = true;
            if (filterVal !== 'all') {
                if (currentType === 'students') matchesFilter = item.branch === filterVal;
                if (currentType === 'events') matchesFilter = item.category === filterVal;
            }

            return matchesSearch && matchesFilter;
        });

        // Sorting logic
        if (sortVal !== 'default') {
            filteredData.sort((a, b) => {
                let valA = a.name || a.title || a.q;
                let valB = b.name || b.title || b.q;
                valA = String(valA).toLowerCase();
                valB = String(valB).toLowerCase();
                
                if (sortVal === 'name_asc') return valA.localeCompare(valB);
                if (sortVal === 'name_desc') return valB.localeCompare(valA);
            });
        }

        currentPage = 1; // Reset to first page
        renderTable();
    }

    // Render Table and Pagination
    function renderTable() {
        statusContainer.innerHTML = '';
        
        if (filteredData.length === 0) {
            tableContainer.innerHTML = '<p style="padding:20px; text-align:center;">No records found.</p>';
            paginationContainer.innerHTML = '';
            return;
        }

        // Pagination calculations
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        const startIndex = (currentPage - 1) * itemsPerPage;
        const paginatedData = filteredData.slice(startIndex, startIndex + itemsPerPage);

        // Generate Table HTML
        let html = '<table><thead><tr>';
        
        // Dynamic Headers
        const keys = Object.keys(paginatedData[0]);
        keys.forEach(key => {
            html += `<th>${key.toUpperCase()}</th>`;
        });
        html += '</tr></thead><tbody>';

        // Dynamic Rows
        paginatedData.forEach(item => {
            html += '<tr>';
            keys.forEach(key => {
                let val = item[key];
                // Styling specific columns
                if (key === 'cpi' && val >= 9) val = `<mark>${val}</mark>`;
                html += `<td>${val}</td>`;
            });
            html += '</tr>';
        });
        
        html += '</tbody></table>';
        tableContainer.innerHTML = html;

        renderPagination(totalPages);
    }

    // Pagination Controls
    function renderPagination(totalPages) {
        if (totalPages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }

        let html = `<button id="prevBtn" ${currentPage === 1 ? 'disabled' : ''}>Previous</button>`;
        
        for (let i = 1; i <= totalPages; i++) {
            html += `<button class="page-btn ${currentPage === i ? 'active' : ''}" data-page="${i}">${i}</button>`;
        }
        
        html += `<button id="nextBtn" ${currentPage === totalPages ? 'disabled' : ''}>Next</button>`;
        paginationContainer.innerHTML = html;

        // Pagination Events
        document.getElementById('prevBtn')?.addEventListener('click', () => {
            if (currentPage > 1) { currentPage--; renderTable(); }
        });
        document.getElementById('nextBtn')?.addEventListener('click', () => {
            if (currentPage < totalPages) { currentPage++; renderTable(); }
        });
        document.querySelectorAll('.page-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                currentPage = parseInt(e.target.getAttribute('data-page'));
                renderTable();
            });
        });
    }

    // UI States
    function showLoading() {
        tableContainer.innerHTML = '';
        paginationContainer.innerHTML = '';
        statusContainer.innerHTML = '<div class="loading">⏳ Fetching Data from JSON...</div>';
    }

    function showError(msg) {
        tableContainer.innerHTML = '';
        paginationContainer.innerHTML = '';
        statusContainer.innerHTML = `<div class="error-message">⚠️ ${msg}</div>`;
    }
});
