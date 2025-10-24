let currentUserId = null;
let mergeUser1 = null;
let mergeUser2 = null;
let dynamicFieldCounter = 0;

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add User';
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    document.getElementById('dynamicFieldsContainer').innerHTML = '';
    dynamicFieldCounter = 0;
}

function addDynamicField() {
    const container = document.getElementById('dynamicFieldsContainer');
    const fieldHtml = `
        <div class="row mb-2 dynamic-field-row">
            <div class="col-md-4">
                <input type="text" class="form-control" name="custom_label_${dynamicFieldCounter}" placeholder="Field Label (e.g., Birth Date)">
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" name="custom_value_${dynamicFieldCounter}" placeholder="Field Value">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeDynamicField(this)">Remove</button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', fieldHtml);
    dynamicFieldCounter++;
}

function removeDynamicField(button) {
    button.closest('.dynamic-field-row').remove();
}

function editUser(id) {
    fetch(`/users/${id}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            document.getElementById('modalTitle').textContent = 'Edit User';
            document.getElementById('userId').value = data.id;
            document.querySelector('[name="name"]').value = data.name;
            document.querySelector('[name="email"]').value = data.email;
            document.querySelector('[name="phone"]').value = data.phone || '';
            
            if (data.gender) {
                document.querySelector(`[name="gender"][value="${data.gender}"]`).checked = true;
            }

            const container = document.getElementById('dynamicFieldsContainer');
            container.innerHTML = '';
            dynamicFieldCounter = 0;
            
            if (data.user_details) {
                data.user_details.forEach(detail => {
                    addDynamicFieldWithData(detail.label, detail.value);
                });
            }

            new bootstrap.Modal(document.getElementById('userModal')).show();
        });
}

function addDynamicFieldWithData(label, value) {
    const container = document.getElementById('dynamicFieldsContainer');
    const fieldHtml = `
        <div class="row mb-2 dynamic-field-row">
            <div class="col-md-4">
                <input type="text" class="form-control" name="custom_label_${dynamicFieldCounter}" value="${label}">
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" name="custom_value_${dynamicFieldCounter}" value="${value}">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeDynamicField(this)">Remove</button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', fieldHtml);
    dynamicFieldCounter++;
}

function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user?')) {
        fetch(`/users/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`tr[data-id="${id}"]`).remove();
                alert(data.message);
            }
        });
    }
}

function filterUsers() {
    const name = document.getElementById('filterName').value;
    const email = document.getElementById('filterEmail').value;
    const gender = document.getElementById('filterGender').value;
    const dynamicField = document.getElementById('filterDynamicField') ? document.getElementById('filterDynamicField').value : '';
    const dynamicValue = document.getElementById('filterDynamicValue') ? document.getElementById('filterDynamicValue').value : '';

    fetch('/users?' + new URLSearchParams({
        name: name,
        email: email,
        gender: gender,
        dynamic_field: dynamicField,
        dynamic_value: dynamicValue
    }), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        updateUsersTable(data.users);
    });
}

function clearFilters() {
    document.getElementById('filterName').value = '';
    document.getElementById('filterEmail').value = '';
    document.getElementById('filterGender').value = '';
    if (document.getElementById('filterDynamicField')) {
        document.getElementById('filterDynamicField').value = '';
    }
    if (document.getElementById('filterDynamicValue')) {
        document.getElementById('filterDynamicValue').value = '';
    }
    filterUsers();
}

function updateUsersTable(users) {
    const tbody = document.getElementById('usersTable');
    tbody.innerHTML = '';
    
    users.forEach(user => {
        const dynamicFields = (user.user_details || []).map(detail => 
            `<small class="badge bg-info">${detail.label}: ${detail.value}</small><br>`
        ).join('');

        const row = `
            <tr data-id="${user.id}">
                <td>${user.name}</td>
                <td>${user.email}</td>
                <td>${user.phone || ''}</td>
                <td>${user.gender ? user.gender.charAt(0).toUpperCase() + user.gender.slice(1) : ''}</td>
                <td>${dynamicFields}</td>
                <td>
                    <a href="/users/${user.id}" class="btn btn-sm btn-success">View</a>
                    <button class="btn btn-sm btn-primary" onclick="editUser(${user.id})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})">Delete</button>
                    <button class="btn btn-sm btn-warning" onclick="initiateMerge(${user.id})">Merge</button>
                    <button class="btn btn-sm btn-info" onclick="showContacts(${user.id})">Contacts</button>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

function showContacts(userId) {
    document.getElementById('currentUserId').value = userId;
    
    fetch(`/users/${userId}/available-contacts`)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('contactSelect');
            select.innerHTML = '<option value="">Select User</option>';
            data.users.forEach(user => {
                select.innerHTML += `<option value="${user.id}">${user.name} (${user.email})</option>`;
            });
        });
    
    loadCurrentContacts(userId);
    new bootstrap.Modal(document.getElementById('contactModal')).show();
}

function loadCurrentContacts(userId) {
    fetch(`/users/${userId}/contacts`)
        .then(response => response.json())
        .then(data => {
            let html = '';
            if (data.contacts.length > 0) {
                html = '<div class="list-group">';
                data.contacts.forEach(contact => {
                    html += `
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${contact.contact_user.name}</strong><br>
                                <small>${contact.contact_user.email}</small>
                            </div>
                            <button class="btn btn-sm btn-danger" onclick="removeContact(${contact.id})">Remove</button>
                        </div>
                    `;
                });
                html += '</div>';
            } else {
                html = '<p class="text-muted">No contacts found.</p>';
            }
            document.getElementById('currentContacts').innerHTML = html;
        });
}

function removeContact(contactId) {
    if (confirm('Remove this contact?')) {
        fetch(`/users/remove-contact/${contactId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const userId = document.getElementById('currentUserId').value;
                loadCurrentContacts(userId);
            }
        });
    }
}

function initiateMerge(userId) {
    mergeUser1 = userId;
    const users = Array.from(document.querySelectorAll('#usersTable tr')).filter(row => 
        row.dataset.id && row.dataset.id != userId
    );
    
    let html = '<p>Select the second user to merge with:</p><div class="list-group">';
    users.forEach(row => {
        const id = row.dataset.id;
        const name = row.cells[0].textContent;
        const email = row.cells[1].textContent;
        html += `<button type="button" class="list-group-item list-group-item-action" onclick="selectSecondUser(${id})">${name} (${email})</button>`;
    });
    html += '</div>';
    
    document.getElementById('mergeUsersContainer').innerHTML = html;
    new bootstrap.Modal(document.getElementById('mergeModal')).show();
}

function selectSecondUser(userId) {
    mergeUser2 = userId;
    
    document.getElementById('mergeUsersContainer').innerHTML = `
        <div class="alert alert-warning">
            <strong>Confirmation:</strong> User ${mergeUser1} will be the master user. 
            User ${mergeUser2} will be merged into it. All dynamic fields will be preserved. Are you sure?
        </div>
    `;
}

function confirmMerge() {
    fetch('/users/merge', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            master_id: mergeUser1,
            secondary_id: mergeUser2
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        }
    });
}

// Form submissions
$(document).ready(function() {
    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const userId = document.getElementById('userId').value;
        const url = userId ? `/users/${userId}` : '/users';
        
        if (userId) {
            formData.append('_method', 'PUT');
        }

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
                filterUsers();
            }
        });
    });

    $('#addContactForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('/users/add-contact', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                const userId = document.getElementById('currentUserId').value;
                loadCurrentContacts(userId);
                
                fetch(`/users/${userId}/available-contacts`)
                    .then(response => response.json())
                    .then(data => {
                        const select = document.getElementById('contactSelect');
                        select.innerHTML = '<option value="">Select User</option>';
                        data.users.forEach(user => {
                            select.innerHTML += `<option value="${user.id}">${user.name} (${user.email})</option>`;
                        });
                    });
                
                document.getElementById('addContactForm').reset();
                document.getElementById('currentUserId').value = userId;
            } else {
                alert(data.message || 'Error adding contact');
            }
        });
    });
});