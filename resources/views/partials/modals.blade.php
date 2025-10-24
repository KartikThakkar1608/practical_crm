<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="userForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="userId" name="user_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name *</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Gender</label>
                                <div>
                                    <input type="radio" name="gender" value="male" id="male"> <label for="male">Male</label>
                                    <input type="radio" name="gender" value="female" id="female"> <label for="female">Female</label>
                                    <input type="radio" name="gender" value="other" id="other"> <label for="other">Other</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Profile Image</label>
                                <input type="file" class="form-control" name="profile_image" accept="image/*">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Additional File</label>
                                <input type="file" class="form-control" name="additional_file">
                            </div>
                        </div>
                    </div>

                    <div id="dynamicFields">
                        <h6>Dynamic Fields</h6>
                        <div id="dynamicFieldsContainer"></div>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="addDynamicField()">Add Dynamic Field</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Contact Modal -->
<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Contacts</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Add New Contact</h6>
                        <form id="addContactForm">
                            <input type="hidden" id="currentUserId" name="user_id">
                            <div class="mb-3">
                                <label class="form-label">Select User to Add as Contact</label>
                                <select class="form-control" name="contact_id" id="contactSelect" required>
                                    <option value="">Select User</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Contact</button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <h6>Current Contacts</h6>
                        <div id="currentContacts"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Merge Modal -->
<div class="modal fade" id="mergeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Merge Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Select which user should be the master:</p>
                <div id="mergeUsersContainer"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmMerge()">Merge Users</button>
            </div>
        </div>
    </div>
</div>