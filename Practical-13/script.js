// Practical 13: Asynchronous AJAX/Fetch CRUD & Dynamic RBAC Navigation
const API_URL = 'api.php';

document.addEventListener('DOMContentLoaded', () => {
    loadMenu();
    loadItems();
});

// -------------------------------------------------------------
// 1. Load Dynamic Menu according to Role (RBAC)
// -------------------------------------------------------------
async function loadMenu() {
    const roleSelector = document.getElementById('roleSelector');
    const role = roleSelector ? roleSelector.value : 'admin';
    const menuUl = document.getElementById('dynamicMenu');

    try {
        const res = await fetch(`${API_URL}?action=get_menu&role=${encodeURIComponent(role)}`);
        const result = await res.json();

        menuUl.innerHTML = '';

        if (result.status === 'success' && Array.isArray(result.data)) {
            result.data.forEach(item => {
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.href = item.link;
                a.textContent = item.title;
                li.appendChild(a);
                menuUl.appendChild(li);
            });
            console.log(`[RBAC] Loaded ${result.data.length} menu items for role: ${role}`);
        } else {
            showAlert('Failed to load navigation menu: ' + (result.message || 'Unknown error'), 'error');
        }
    } catch (err) {
        console.error('Menu Fetch Error:', err);
        showAlert('Network error while loading menu.', 'error');
    }
}

// -------------------------------------------------------------
// 2. Read Items (Inventory List)
// -------------------------------------------------------------
async function loadItems() {
    const tbody = document.getElementById('itemsTableBody');
    tbody.innerHTML = `<tr><td colspan="5" class="empty-state">⏳ Loading inventory data from database...</td></tr>`;

    try {
        const res = await fetch(`${API_URL}?action=read`);
        const result = await res.json();

        tbody.innerHTML = '';

        if (result.status === 'success' && Array.isArray(result.data)) {
            if (result.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="empty-state">No inventory items found. Add one using the form.</td></tr>`;
                return;
            }

            result.data.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>#${item.id}</strong></td>
                    <td><strong>${escapeHtml(item.name)}</strong></td>
                    <td><span class="badge-cat">${escapeHtml(item.category)}</span></td>
                    <td class="price-text">$${parseFloat(item.price).toFixed(2)}</td>
                    <td>
                        <div class="actions-cell">
                            <button type="button" class="btn btn-edit" onclick="editItem(${item.id}, '${escapeJs(item.name)}', '${escapeJs(item.category)}', ${item.price})">Edit</button>
                            <button type="button" class="btn btn-danger" onclick="deleteItem(${item.id})">Delete</button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
            console.log(`[CRUD] Rendered ${result.data.length} items without page reload.`);
        } else {
            tbody.innerHTML = `<tr><td colspan="5" class="empty-state" style="color:red;">Failed to load items: ${result.message || ''}</td></tr>`;
            showAlert(result.message || 'Failed to load items', 'error');
        }
    } catch (err) {
        console.error('Fetch read error:', err);
        tbody.innerHTML = `<tr><td colspan="5" class="empty-state" style="color:red;">Error connecting to API. Check console.</td></tr>`;
        showAlert('Network error while fetching inventory list.', 'error');
    }
}

// -------------------------------------------------------------
// 3. Create or Update Item (Zero Page Reload Fetch POST)
// -------------------------------------------------------------
async function saveItem(event) {
    event.preventDefault(); // Prevent standard browser page reload

    const id = document.getElementById('itemId').value;
    const name = document.getElementById('itemName').value.trim();
    const category = document.getElementById('itemCategory').value.trim();
    const price = document.getElementById('itemPrice').value.trim();
    const submitBtn = document.getElementById('submitBtn');

    if (!name || !category || price === '') {
        showAlert('Please fill in all required fields.', 'error');
        return;
    }

    const action = id ? 'update' : 'create';
    const payload = { id, name, category, price: parseFloat(price) };

    submitBtn.disabled = true;
    showAlert(id ? '⏳ Updating item via Fetch API...' : '⏳ Creating item via Fetch API...', 'loading');

    try {
        const res = await fetch(`${API_URL}?action=${action}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const result = await res.json();
        submitBtn.disabled = false;

        if (result.status === 'success') {
            showAlert(`✅ ${result.message}`, 'success');
            resetForm();
            loadItems();
        } else {
            showAlert(`⚠️ ${result.message || 'Operation failed'}`, 'error');
        }
    } catch (err) {
        submitBtn.disabled = false;
        console.error('Save error:', err);
        showAlert('Network error while saving item.', 'error');
    }
}

// -------------------------------------------------------------
// 4. Set Form for Editing (Populate form inputs)
// -------------------------------------------------------------
function editItem(id, name, category, price) {
    document.getElementById('itemId').value = id;
    document.getElementById('itemName').value = name;
    document.getElementById('itemCategory').value = category;
    document.getElementById('itemPrice').value = price;

    document.getElementById('formTitle').innerText = 'Edit Item';
    document.getElementById('submitBtn').innerText = 'Update Item';
    document.getElementById('cancelBtn').style.display = 'block';

    const modeBadge = document.getElementById('formModeBadge');
    if (modeBadge) {
        modeBadge.innerText = 'UPDATE';
        modeBadge.style.background = '#fef3c7';
        modeBadge.style.color = '#92400e';
    }

    showAlert(`✏️ Editing item #${id} (${name}). Update fields and click 'Update Item'.`, 'info');
    document.getElementById('itemName').focus();
}

// -------------------------------------------------------------
// 5. Delete Item (Fetch POST with Confirmation Dialog)
// -------------------------------------------------------------
async function deleteItem(id) {
    if (!confirm(`Are you sure you want to delete item #${id}?`)) {
        return;
    }

    showAlert(`⏳ Deleting item #${id} via Fetch API...`, 'loading');

    try {
        const res = await fetch(`${API_URL}?action=delete`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });

        const result = await res.json();

        if (result.status === 'success') {
            showAlert(`✅ ${result.message}`, 'success');
            loadItems();
        } else {
            showAlert(`⚠️ ${result.message || 'Deletion failed'}`, 'error');
        }
    } catch (err) {
        console.error('Delete error:', err);
        showAlert('Network error while deleting item.', 'error');
    }
}

// -------------------------------------------------------------
// 6. Reset Form State
// -------------------------------------------------------------
function resetForm() {
    document.getElementById('itemId').value = '';
    document.getElementById('itemForm').reset();
    document.getElementById('formTitle').innerText = 'Add Item';
    document.getElementById('submitBtn').innerText = 'Save Item';
    document.getElementById('cancelBtn').style.display = 'none';

    const modeBadge = document.getElementById('formModeBadge');
    if (modeBadge) {
        modeBadge.innerText = 'CREATE';
        modeBadge.style.background = '#e2e8f0';
        modeBadge.style.color = '#475569';
    }
}

// -------------------------------------------------------------
// 7. Alert / Notification System
// -------------------------------------------------------------
let alertTimer = null;
function showAlert(message, type = 'info', autoCloseMs = 5000) {
    const alertBox = document.getElementById('statusAlert');
    if (!alertBox) return;

    if (alertTimer) {
        clearTimeout(alertTimer);
    }

    alertBox.className = `alert alert-${type}`;
    alertBox.innerHTML = message;
    alertBox.style.display = 'flex';

    if (autoCloseMs && type !== 'loading') {
        alertTimer = setTimeout(() => {
            alertBox.style.display = 'none';
        }, autoCloseMs);
    }
}

// -------------------------------------------------------------
// Lab Report Screenshot Helpers (Error & Loading States)
// -------------------------------------------------------------
function simulateLoadingState() {
    showAlert('⏳ Fetching inventory data from server... (Loading State Active)', 'loading', 0);
    const tbody = document.getElementById('itemsTableBody');
    tbody.innerHTML = `<tr><td colspan="5" class="empty-state">⏳ Fetching latest records from MySQL via Fetch API...</td></tr>`;
    setTimeout(() => {
        loadItems();
        showAlert('✅ Data loaded successfully!', 'success');
    }, 3000);
}

function simulateErrorState() {
    showAlert('⚠️ Validation / Network Error: Missing required fields or 400 Bad Request.', 'error', 8000);
}

// Security / Utility helpers
function escapeHtml(str) {
    if (typeof str !== 'string') return str;
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function escapeJs(str) {
    if (typeof str !== 'string') return str;
    return str.replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '\\"');
}
