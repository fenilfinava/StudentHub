// Practical 13: StudentHub AJAX/Fetch CRUD & Dynamic RBAC Navigation
const API_URL = 'api.php';
let currentEntity = 'students'; // default entity: StudentHub Students

document.addEventListener('DOMContentLoaded', () => {
    loadMenu();
    loadData();
});

// -------------------------------------------------------------
// 1. Dynamic Navigation Menu with RBAC (Fetch GET)
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
            console.log(`[RBAC] Loaded ${result.data.length} StudentHub menu links for: ${role}`);
        } else {
            showAlert('Failed to load navigation menu.', 'error');
        }
    } catch (err) {
        console.error('Menu Fetch Error:', err);
        showAlert('Network error while loading RBAC menu.', 'error');
    }
}

// -------------------------------------------------------------
// 2. Read Records (Fetch GET without page reload)
// -------------------------------------------------------------
async function loadData() {
    const tableHead = document.getElementById('tableHead');
    const tbody = document.getElementById('itemsTableBody');

    // Render Table Headers
    if (currentEntity === 'students') {
        tableHead.innerHTML = `
            <tr>
                <th style="width: 50px;">ID</th>
                <th>Student Name</th>
                <th>Email Address</th>
                <th>Course / Branch</th>
                <th style="width: 90px;">Gender</th>
                <th style="width: 90px;">Status</th>
                <th style="width: 140px;">Actions</th>
            </tr>
        `;
    } else {
        tableHead.innerHTML = `
            <tr>
                <th style="width: 50px;">ID</th>
                <th>Resource Name</th>
                <th>Category</th>
                <th style="width: 110px;">Price</th>
                <th style="width: 140px;">Actions</th>
            </tr>
        `;
    }

    tbody.innerHTML = `<tr><td colspan="7" class="empty-cell">⏳ Fetching live records from MySQL via Fetch API...</td></tr>`;

    try {
        const res = await fetch(`${API_URL}?action=read&type=${currentEntity}`);
        const result = await res.json();

        tbody.innerHTML = '';

        if (result.status === 'success' && Array.isArray(result.data)) {
            if (result.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="empty-cell">No records found. Add one using the form on the left.</td></tr>`;
                return;
            }

            result.data.forEach(row => {
                const tr = document.createElement('tr');
                if (currentEntity === 'students') {
                    const statusClass = (row.status || 'Active').toLowerCase() === 'active' ? 'badge-status-active' : 'badge-status-inactive';
                    tr.innerHTML = `
                        <td><strong>#${row.id}</strong></td>
                        <td><strong>${escapeHtml(row.name)}</strong></td>
                        <td>${escapeHtml(row.email)}</td>
                        <td><span class="badge-tag">${escapeHtml(row.course)}</span></td>
                        <td>${escapeHtml(row.gender)}</td>
                        <td><span class="${statusClass}">${escapeHtml(row.status || 'Active')}</span></td>
                        <td>
                            <div class="actions-cluster">
                                <button type="button" class="btn btn-action-edit" onclick="editStudent(${row.id}, '${escapeJs(row.name)}', '${escapeJs(row.email)}', '${escapeJs(row.course)}', '${escapeJs(row.gender)}')">Edit</button>
                                <button type="button" class="btn btn-action-delete" onclick="deleteRecord(${row.id})">Delete</button>
                            </div>
                        </td>
                    `;
                } else {
                    tr.innerHTML = `
                        <td><strong>#${row.id}</strong></td>
                        <td><strong>${escapeHtml(row.name)}</strong></td>
                        <td><span class="badge-tag">${escapeHtml(row.category)}</span></td>
                        <td style="font-weight:700; color:#047857;">$${parseFloat(row.price).toFixed(2)}</td>
                        <td>
                            <div class="actions-cluster">
                                <button type="button" class="btn btn-action-edit" onclick="editItem(${row.id}, '${escapeJs(row.name)}', '${escapeJs(row.category)}', ${row.price})">Edit</button>
                                <button type="button" class="btn btn-action-delete" onclick="deleteRecord(${row.id})">Delete</button>
                            </div>
                        </td>
                    `;
                }
                tbody.appendChild(tr);
            });
            console.log(`[CRUD] Asynchronously rendered ${result.data.length} records (${currentEntity}).`);
        } else {
            tbody.innerHTML = `<tr><td colspan="7" class="empty-cell" style="color:red;">Failed to load records.</td></tr>`;
            showAlert(result.message || 'Error loading records', 'error');
        }
    } catch (err) {
        console.error('Fetch read error:', err);
        tbody.innerHTML = `<tr><td colspan="7" class="empty-cell" style="color:red;">Error connecting to API.</td></tr>`;
        showAlert('Network error while fetching data.', 'error');
    }
}

// -------------------------------------------------------------
// 3. Create or Update Record (Fetch POST without page reload)
// -------------------------------------------------------------
async function handleFormSubmit(event) {
    event.preventDefault(); // Stop standard page reload

    const id = document.getElementById('recordId').value;
    const submitBtn = document.getElementById('submitBtn');
    let payload = {};

    if (currentEntity === 'students') {
        const name = document.getElementById('recordName').value.trim();
        const email = document.getElementById('recordEmail').value.trim();
        const course = document.getElementById('recordCourse').value;
        const gender = document.getElementById('recordGender').value;

        if (!name || !email || !course || !gender) {
            showAlert('Please fill in all student details.', 'error');
            return;
        }
        payload = { id, name, email, course, gender };
    } else {
        const name = document.getElementById('itemName').value.trim();
        const category = document.getElementById('itemCategory').value.trim();
        const price = document.getElementById('itemPrice').value.trim();

        if (!name || !category || price === '') {
            showAlert('Please fill in all item fields.', 'error');
            return;
        }
        payload = { id, name, category, price: parseFloat(price) };
    }

    const action = id ? 'update' : 'create';
    submitBtn.disabled = true;
    showAlert(id ? '⏳ Updating record via Fetch API...' : '⏳ Adding record via Fetch API...', 'loading');

    try {
        const res = await fetch(`${API_URL}?action=${action}&type=${currentEntity}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const result = await res.json();
        submitBtn.disabled = false;

        if (result.status === 'success') {
            showAlert(`✅ ${result.message}`, 'success');
            resetForm();
            loadData();
        } else {
            showAlert(`⚠️ ${result.message || 'Operation failed'}`, 'error');
        }
    } catch (err) {
        submitBtn.disabled = false;
        console.error('Save error:', err);
        showAlert('Network error while saving record.', 'error');
    }
}

// -------------------------------------------------------------
// 4. Set Form for Editing Student
// -------------------------------------------------------------
function editStudent(id, name, email, course, gender) {
    document.getElementById('recordId').value = id;
    document.getElementById('recordName').value = name;
    document.getElementById('recordEmail').value = email;
    document.getElementById('recordCourse').value = course;
    document.getElementById('recordGender').value = gender;

    document.getElementById('formTitle').innerText = 'Edit Student Details';
    document.getElementById('submitBtn').innerText = 'Update Student Record';
    document.getElementById('cancelBtn').style.display = 'block';

    const modeBadge = document.getElementById('formModeBadge');
    if (modeBadge) {
        modeBadge.innerText = 'UPDATE';
        modeBadge.style.background = '#fef3c7';
        modeBadge.style.color = '#92400e';
    }

    showAlert(`✏️ Editing student #${id} (${name}). Update details and click 'Update Student Record'.`, 'info');
    document.getElementById('recordName').focus();
}

// -------------------------------------------------------------
// 5. Set Form for Editing Item (PDF Spec mode)
// -------------------------------------------------------------
function editItem(id, name, category, price) {
    document.getElementById('recordId').value = id;
    document.getElementById('itemName').value = name;
    document.getElementById('itemCategory').value = category;
    document.getElementById('itemPrice').value = price;

    document.getElementById('formTitle').innerText = 'Edit Campus Item';
    document.getElementById('submitBtn').innerText = 'Update Item';
    document.getElementById('cancelBtn').style.display = 'block';

    const modeBadge = document.getElementById('formModeBadge');
    if (modeBadge) {
        modeBadge.innerText = 'UPDATE';
        modeBadge.style.background = '#fef3c7';
        modeBadge.style.color = '#92400e';
    }

    showAlert(`✏️ Editing item #${id} (${name}). Update and click 'Update Item'.`, 'info');
    document.getElementById('itemName').focus();
}

// -------------------------------------------------------------
// 6. Delete Record (Fetch POST with confirmation)
// -------------------------------------------------------------
async function deleteRecord(id) {
    const itemLabel = currentEntity === 'students' ? 'student record' : 'item';
    if (!confirm(`Are you sure you want to delete this ${itemLabel} (#${id})?`)) {
        return;
    }

    showAlert(`⏳ Deleting ${itemLabel} #${id} via Fetch API...`, 'loading');

    try {
        const res = await fetch(`${API_URL}?action=delete&type=${currentEntity}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });

        const result = await res.json();

        if (result.status === 'success') {
            showAlert(`✅ ${result.message}`, 'success');
            loadData();
        } else {
            showAlert(`⚠️ ${result.message || 'Deletion failed'}`, 'error');
        }
    } catch (err) {
        console.error('Delete error:', err);
        showAlert('Network error while deleting.', 'error');
    }
}

// -------------------------------------------------------------
// 7. Reset Form State
// -------------------------------------------------------------
function resetForm() {
    document.getElementById('recordId').value = '';
    document.getElementById('crudForm').reset();

    const isStudent = currentEntity === 'students';
    document.getElementById('formTitle').innerText = isStudent ? 'Add New Student' : 'Add Campus Item';
    document.getElementById('submitBtn').innerText = isStudent ? 'Save Student Record' : 'Save Item';
    document.getElementById('cancelBtn').style.display = 'none';

    const modeBadge = document.getElementById('formModeBadge');
    if (modeBadge) {
        modeBadge.innerText = 'CREATE';
        modeBadge.style.background = '#e0f2fe';
        modeBadge.style.color = '#0369a1';
    }
}

// -------------------------------------------------------------
// 8. Switch Entity (Students vs Items)
// -------------------------------------------------------------
function switchEntity(entity) {
    currentEntity = entity;
    resetForm();

    const tabStudents = document.getElementById('tabStudents');
    const tabItems = document.getElementById('tabItems');
    const studentFields = document.getElementById('studentFields');
    const itemFields = document.getElementById('itemFields');
    const tableTitle = document.getElementById('tableTitle');

    if (entity === 'students') {
        tabStudents.classList.add('active');
        tabItems.classList.remove('active');
        studentFields.style.display = 'block';
        itemFields.style.display = 'none';
        tableTitle.innerText = 'Student Records (Live MySQL)';

        document.getElementById('recordName').required = true;
        document.getElementById('recordEmail').required = true;
        document.getElementById('recordCourse').required = true;
        document.getElementById('recordGender').required = true;

        document.getElementById('itemName').required = false;
        document.getElementById('itemCategory').required = false;
        document.getElementById('itemPrice').required = false;
    } else {
        tabStudents.classList.remove('active');
        tabItems.classList.add('active');
        studentFields.style.display = 'none';
        itemFields.style.display = 'block';
        tableTitle.innerText = 'Campus Resources / Inventory (Live MySQL)';

        document.getElementById('recordName').required = false;
        document.getElementById('recordEmail').required = false;
        document.getElementById('recordCourse').required = false;
        document.getElementById('recordGender').required = false;

        document.getElementById('itemName').required = true;
        document.getElementById('itemCategory').required = true;
        document.getElementById('itemPrice').required = true;
    }

    loadData();
}

// -------------------------------------------------------------
// 9. Status & Live Alert Notifications
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
// 10. Lab Report Screenshot Helpers
// -------------------------------------------------------------
function simulateLoadingState() {
    showAlert('⏳ Fetching StudentHub records from MySQL... (Loading State Active)', 'loading', 0);
    const tbody = document.getElementById('itemsTableBody');
    tbody.innerHTML = `<tr><td colspan="7" class="empty-cell">⏳ Fetching live records via Fetch API without page reload...</td></tr>`;
    setTimeout(() => {
        loadData();
        showAlert('✅ Student records loaded successfully!', 'success');
    }, 2800);
}

function simulateErrorState() {
    showAlert('⚠️ Validation Error: Missing required student fields or network timeout (400 Bad Request).', 'error', 8000);
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
