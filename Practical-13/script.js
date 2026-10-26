// Practical 13: StudentHub AJAX/Fetch CRUD & Dynamic RBAC Navigation
const API_URL = 'api.php';

const iconMap = {
    'Student Dashboard': '📊',
    'My Profile': '👤',
    'Campus Events': '🎪',
    'Faculty Portal': '🏛️',
    'Student Attendance': '📋',
    'Admin Console': '⚙️',
    'Student Records (CRUD)': '👥',
    'User Management': '🔐',
    'System Settings': '🛠️'
};

document.addEventListener('DOMContentLoaded', () => {
    handleRoleChange();
    loadData();
});

// -------------------------------------------------------------
// 1. Role Change Handler (Updates Badge & Dynamic Menu)
// -------------------------------------------------------------
function handleRoleChange() {
    const roleSelector = document.getElementById('roleSelector');
    const role = roleSelector ? roleSelector.value : 'admin';
    const roleBadge = document.getElementById('roleBadge');

    if (roleBadge) {
        if (role === 'admin') {
            roleBadge.className = 'badge badge-admin';
            roleBadge.innerText = '👑 ADMIN PORTAL';
        } else if (role === 'editor') {
            roleBadge.className = 'badge badge-editor';
            roleBadge.innerText = '👨‍🏫 FACULTY PORTAL';
        } else {
            roleBadge.className = 'badge badge-student';
            roleBadge.innerText = '👨‍🎓 STUDENT PORTAL';
        }
    }

    loadMenu();
}

// -------------------------------------------------------------
// 2. Dynamic RBAC Navigation Menu (Fetch GET)
// -------------------------------------------------------------
async function loadMenu() {
    const roleSelector = document.getElementById('roleSelector');
    const role = roleSelector ? roleSelector.value : 'admin';
    const menuUl = document.getElementById('dynamicMenu');
    const moduleCount = document.getElementById('moduleCount');

    try {
        const res = await fetch(`${API_URL}?action=get_menu&role=${encodeURIComponent(role)}`);
        const result = await res.json();

        menuUl.innerHTML = '';

        if (result.status === 'success' && Array.isArray(result.data)) {
            if (moduleCount) {
                moduleCount.innerText = `${result.data.length} Modules Active`;
            }

            result.data.forEach(item => {
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.href = item.link;

                const icon = iconMap[item.title] || '🔗';
                a.innerHTML = `<span style="font-size:14px;">${icon}</span> <span>${item.title}</span>`;

                if (item.title === 'Student Records (CRUD)') {
                    a.classList.add('active-link');
                }

                li.appendChild(a);
                menuUl.appendChild(li);
            });
            console.log(`[RBAC] Loaded ${result.data.length} modules for: ${role}`);
        } else {
            showAlert('Failed to load navigation menu.', 'error');
        }
    } catch (err) {
        console.error('Menu Fetch Error:', err);
        showAlert('Network error while loading RBAC menu.', 'error');
    }
}

// -------------------------------------------------------------
// 3. Read Student Records (Fetch GET without page reload)
// -------------------------------------------------------------
async function loadData() {
    const tbody = document.getElementById('itemsTableBody');
    tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:25px; color:#718096; font-style:italic;">⏳ Loading student records from database...</td></tr>`;

    try {
        const res = await fetch(`${API_URL}?action=read&type=students`);
        const result = await res.json();

        tbody.innerHTML = '';

        if (result.status === 'success' && Array.isArray(result.data)) {
            if (result.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:25px; color:#718096;">No student records found. Add one above.</td></tr>`;
                return;
            }

            result.data.forEach(row => {
                const tr = document.createElement('tr');
                const statusClass = (row.status || 'Active').toLowerCase() === 'active' ? 'badge-status-active' : 'badge-status-inactive';
                tr.innerHTML = `
                    <td>${row.id}</td>
                    <td><strong>${escapeHtml(row.name)}</strong></td>
                    <td>${escapeHtml(row.email)}</td>
                    <td>${escapeHtml(row.course)}</td>
                    <td>${escapeHtml(row.gender)}</td>
                    <td><span class="${statusClass}">${escapeHtml(row.status || 'Active')}</span></td>
                    <td class="action-links">
                        <a href="javascript:void(0)" onclick="editStudent(${row.id}, '${escapeJs(row.name)}', '${escapeJs(row.email)}', '${escapeJs(row.course)}', '${escapeJs(row.gender)}')">Edit</a>
                        |
                        <a href="javascript:void(0)" class="btn-delete" onclick="deleteStudent(${row.id})">Delete</a>
                    </td>
                `;
                tbody.appendChild(tr);
            });
            console.log(`[CRUD] Asynchronously rendered ${result.data.length} student records.`);
        } else {
            tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; color:red;">Failed to load records: ${result.message || ''}</td></tr>`;
            showAlert(result.message || 'Error loading records', 'error');
        }
    } catch (err) {
        console.error('Fetch read error:', err);
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; color:red;">Error connecting to API. Check console.</td></tr>`;
        showAlert('Network error while fetching student list.', 'error');
    }
}

// -------------------------------------------------------------
// 4. Create or Update Student (Fetch POST without page reload)
// -------------------------------------------------------------
async function handleFormSubmit(event) {
    event.preventDefault(); // Prevent standard browser page reload

    const id = document.getElementById('recordId').value;
    const name = document.getElementById('recordName').value.trim();
    const email = document.getElementById('recordEmail').value.trim();
    const course = document.getElementById('recordCourse').value;
    const gender = document.getElementById('recordGender').value;
    const submitBtn = document.getElementById('submitBtn');

    if (!name || !email || !course || !gender) {
        showAlert('Please fill in all student details.', 'error');
        return;
    }

    const action = id ? 'update' : 'create';
    const payload = { id, name, email, course, gender };

    submitBtn.disabled = true;
    showAlert(id ? '⏳ Updating student details via Fetch API...' : '⏳ Adding new student via Fetch API...', 'loading');

    try {
        const res = await fetch(`${API_URL}?action=${action}&type=students`, {
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
        showAlert('Network error while saving student record.', 'error');
    }
}

// -------------------------------------------------------------
// 5. Populate Form for Editing Student
// -------------------------------------------------------------
function editStudent(id, name, email, course, gender) {
    document.getElementById('recordId').value = id;
    document.getElementById('recordName').value = name;
    document.getElementById('recordEmail').value = email;
    document.getElementById('recordCourse').value = course;
    document.getElementById('recordGender').value = gender;

    document.getElementById('formTitle').innerText = 'Edit Student Details';
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.innerText = 'Save Changes';
    submitBtn.className = 'btn btn-success';
    document.getElementById('cancelBtn').style.display = 'inline-block';

    showAlert(`✏️ Editing student #${id} (${name}). Update details and click 'Save Changes'.`, 'loading', 4000);
    document.getElementById('recordName').focus();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// -------------------------------------------------------------
// 6. Delete Student Record (Fetch POST with Confirmation)
// -------------------------------------------------------------
async function deleteStudent(id) {
    if (!confirm('Are you sure you want to delete this student record?')) {
        return;
    }

    showAlert(`⏳ Deleting student #${id} via Fetch API...`, 'loading');

    try {
        const res = await fetch(`${API_URL}?action=delete&type=students`, {
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
        showAlert('Network error while deleting student record.', 'error');
    }
}

// -------------------------------------------------------------
// 7. Reset Form State
// -------------------------------------------------------------
function resetForm() {
    document.getElementById('recordId').value = '';
    document.getElementById('crudForm').reset();
    document.getElementById('formTitle').innerText = 'Add New Student';
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.innerText = '+ Add Student';
    submitBtn.className = 'btn btn-success';
    document.getElementById('cancelBtn').style.display = 'none';
}

// -------------------------------------------------------------
// 8. Live Notification Alerts
// -------------------------------------------------------------
let alertTimer = null;
function showAlert(message, type = 'info', autoCloseMs = 5000) {
    const alertBox = document.getElementById('statusAlert');
    if (!alertBox) return;

    if (alertTimer) {
        clearTimeout(alertTimer);
    }

    alertBox.className = `alert-${type}`;
    alertBox.innerHTML = message;
    alertBox.style.display = 'flex';

    if (autoCloseMs && type !== 'loading') {
        alertTimer = setTimeout(() => {
            alertBox.style.display = 'none';
        }, autoCloseMs);
    }
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
