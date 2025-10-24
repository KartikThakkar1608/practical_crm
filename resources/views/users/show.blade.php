@extends('layouts.app')

@section('title', 'User Details - ' . $user->name)

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>User Details</h1>
            <a href="/" class="btn btn-secondary">Back to Users</a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Basic Information -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Basic Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Name:</strong></div>
                    <div class="col-sm-8">{{ $user->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Email:</strong></div>
                    <div class="col-sm-8">{{ $user->email }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Phone:</strong></div>
                    <div class="col-sm-8">{{ $user->phone ?: 'Not provided' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Gender:</strong></div>
                    <div class="col-sm-8">{{ $user->gender ? ucfirst($user->gender) : 'Not specified' }}</div>
                </div>
                @if($user->profile_image)
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Profile Image:</strong></div>
                    <div class="col-sm-8">
                        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile" class="img-thumbnail" style="max-width: 150px;">
                    </div>
                </div>
                @endif
                @if($user->additional_file)
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Additional File:</strong></div>
                    <div class="col-sm-8">
                        <a href="{{ asset('storage/' . $user->additional_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">Download File</a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Dynamic Fields -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Dynamic Fields</h5>
            </div>
            <div class="card-body">
                @if($user->userDetails->count() > 0)
                    @foreach($user->userDetails as $detail)
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>{{ $detail->label }}:</strong></div>
                        <div class="col-sm-8">{{ $detail->value }}</div>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted">No dynamic fields added.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Contacts Section -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Active Contacts ({{ $user->contacts->where('is_merged', false)->count() }})</h5>
            </div>
            <div class="card-body">
                @if($user->contacts->where('is_merged', false)->count() > 0)
                    @foreach($user->contacts->where('is_merged', false) as $contact)
                    <div class="card border-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title">{{ $contact->contactUser->name }}</h6>
                            <p class="card-text">
                                <small class="text-muted">{{ $contact->contactUser->email }}</small><br>
                                @if($contact->contactUser->phone)
                                    <small class="text-muted">{{ $contact->contactUser->phone }}</small><br>
                                @endif
                                @if($contact->contactUser->gender)
                                    <small class="text-muted">{{ ucfirst($contact->contactUser->gender) }}</small>
                                @endif
                            </p>
                            @if($contact->contactUser->userDetails->count() > 0)
                                <div class="mt-2">
                                    @foreach($contact->contactUser->userDetails as $detail)
                                        <span class="badge bg-info me-1">{{ $detail->label }}: {{ $detail->value }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <div class="mt-2">
                                <a href="/users/{{ $contact->contactUser->id }}" class="btn btn-sm btn-outline-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted">No active contacts.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Merged Contacts ({{ $mergedContacts->count() }})</h5>
            </div>
            <div class="card-body">
                @if($mergedContacts->count() > 0)
                    @foreach($mergedContacts as $mergedUser)
                    <div class="card border-warning mb-3">
                        <div class="card-body">
                            <h6 class="card-title">
                                {{ $mergedUser->name }}
                                <span class="badge bg-warning text-dark ms-2">Merged</span>
                            </h6>
                            <p class="card-text">
                                <small class="text-muted">{{ $mergedUser->email }}</small><br>
                                @if($mergedUser->phone)
                                    <small class="text-muted">{{ $mergedUser->phone }}</small><br>
                                @endif
                                @if($mergedUser->gender)
                                    <small class="text-muted">{{ ucfirst($mergedUser->gender) }}</small>
                                @endif
                            </p>
                            @if($mergedUser->userDetails->count() > 0)
                                <div class="mt-2">
                                    @foreach($mergedUser->userDetails as $detail)
                                        <span class="badge bg-secondary me-1">{{ $detail->label }}: {{ $detail->value }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <div class="mt-2">
                                <small class="text-muted">Merged on: {{ $mergedUser->updated_at->format('M d, Y') }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted">No merged contacts.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="d-flex gap-2">
            <button class="btn btn-primary" onclick="editUser({{ $user->id }})">Edit User</button>
            <button class="btn btn-info" onclick="showContacts({{ $user->id }})">Manage Contacts</button>
            <button class="btn btn-warning" onclick="initiateMerge({{ $user->id }})">Merge User</button>
        </div>
    </div>
</div>
@endsection