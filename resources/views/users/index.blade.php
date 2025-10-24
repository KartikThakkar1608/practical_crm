@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<h1>User Management</h1>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <input type="text" id="filterName" class="form-control" placeholder="Search by name">
            </div>
            <div class="col-md-3">
                <input type="text" id="filterEmail" class="form-control" placeholder="Search by email">
            </div>
            <div class="col-md-3">
                <select id="filterGender" class="form-control">
                    <option value="">All Genders</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-primary" onclick="filterUsers()">Filter</button>
                <button type="button" class="btn btn-secondary" onclick="clearFilters()">Clear</button>
            </div>
        </div>
        @if($dynamicFields->count() > 0)
        <div class="row">
            <div class="col-md-4">
                <select id="filterDynamicField" class="form-control">
                    <option value="">Select Dynamic Field</option>
                    @foreach($dynamicFields as $field)
                        <option value="{{ $field->key }}">{{ $field->label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" id="filterDynamicValue" class="form-control" placeholder="Enter field value">
            </div>
            <div class="col-md-4">
                <small class="text-muted">Filter by dynamic fields</small>
            </div>
        </div>
        @endif
    </div>
</div>

<button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openAddModal()">
    Add New User
</button>

<!-- Users Table -->
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Gender</th>
                <th>Dynamic Fields</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="usersTable">
            @foreach($users as $user)
            <tr data-id="{{ $user->id }}">
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone }}</td>
                <td>{{ ucfirst($user->gender) }}</td>
                <td>
                    @foreach($user->userDetails as $detail)
                        <small class="badge bg-info">{{ $detail->label }}: {{ $detail->value }}</small><br>
                    @endforeach
                </td>
                <td>
                    <a href="/users/{{ $user->id }}" class="btn btn-sm btn-success">View</a>
                    <button class="btn btn-sm btn-primary" onclick="editUser({{ $user->id }})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteUser({{ $user->id }})">Delete</button>
                    <button class="btn btn-sm btn-warning" onclick="initiateMerge({{ $user->id }})">Merge</button>
                    <button class="btn btn-sm btn-info" onclick="showContacts({{ $user->id }})">Contacts</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection