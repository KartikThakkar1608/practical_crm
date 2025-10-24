<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\AddContactRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('userDetails')->whereNotNull('id');

        if ($request->ajax()) {
            if ($request->name) {
                $query->where('name', 'like', '%' . $request->name . '%');
            }
            if ($request->email) {
                $query->where('email', 'like', '%' . $request->email . '%');
            }
            if ($request->gender) {
                $query->where('gender', $request->gender);
            }
            if ($request->dynamic_field && $request->dynamic_value) {
                $query->whereHas('userDetails', function($q) use ($request) {
                    $q->where('key', $request->dynamic_field)
                      ->where('value', 'like', '%' . $request->dynamic_value . '%');
                });
            }

            $users = $query->get();
            return response()->json(['users' => $users]);
        }

        // For contact modal - return all users as JSON
        if ($request->expectsJson()) {
            $users = $query->get();
            return response()->json(['users' => $users]);
        }

        $users = $query->get();
        $dynamicFields = UserDetail::select('key', 'label')->groupBy('key', 'label')->get();
        return view('users.index', compact('users', 'dynamicFields'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->only(['name', 'email', 'phone', 'gender']);
        $data['password'] = Hash::make('password123');

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }

        if ($request->hasFile('additional_file')) {
            $data['additional_file'] = $request->file('additional_file')->store('files', 'public');
        }

        $user = User::create($data);

        // Handle dynamic fields
        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'custom_label_') === 0 && $value) {
                $index = str_replace('custom_label_', '', $key);
                $fieldValue = $request->input("custom_value_$index");
                if ($fieldValue) {
                    $fieldKey = strtolower(str_replace(' ', '_', $value));
                    UserDetail::create([
                        'user_id' => $user->id,
                        'key' => $fieldKey,
                        'label' => $value,
                        'value' => $fieldValue
                    ]);
                }
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'User created successfully']);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    public function show(User $user, Request $request)
    {
        $user->load(['userDetails', 'contacts.contactUser']);
        $mergedContacts = User::with('userDetails')
                               ->where('email', 'like', '%_merged_%')
                               ->get();
        
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json($user);
        }
        
        return view('users.show', compact('user', 'mergedContacts'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {


        $data = $request->only(['name', 'email', 'phone', 'gender']);

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $data['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }

        if ($request->hasFile('additional_file')) {
            if ($user->additional_file) {
                Storage::disk('public')->delete($user->additional_file);
            }
            $data['additional_file'] = $request->file('additional_file')->store('files', 'public');
        }

        $user->update($data);

        // Update dynamic fields
        $user->userDetails()->delete();
        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'custom_label_') === 0 && $value) {
                $index = str_replace('custom_label_', '', $key);
                $fieldValue = $request->input("custom_value_$index");
                if ($fieldValue) {
                    $fieldKey = strtolower(str_replace(' ', '_', $value));
                    UserDetail::create([
                        'user_id' => $user->id,
                        'key' => $fieldKey,
                        'label' => $value,
                        'value' => $fieldValue
                    ]);
                }
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'User updated successfully']);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }
        if ($user->additional_file) {
            Storage::disk('public')->delete($user->additional_file);
        }

        $user->delete();

        return response()->json(['success' => true, 'message' => 'User deleted successfully']);
    }

    public function addContact(AddContactRequest $request)
    {


        $existing = Contact::where('user_id', $request->user_id)
                          ->where('contact_id', $request->contact_id)
                          ->first();

        if ($existing) {
            return response()->json(['success' => false, 'message' => 'Contact already exists']);
        }

        Contact::create([
            'user_id' => $request->user_id,
            'contact_id' => $request->contact_id
        ]);

        return response()->json(['success' => true, 'message' => 'Contact added successfully']);
    }

    public function removeContact($contactId)
    {
        $contact = Contact::find($contactId);
        if ($contact) {
            $contact->delete();
            return response()->json(['success' => true, 'message' => 'Contact removed successfully']);
        }
        return response()->json(['success' => false, 'message' => 'Contact not found']);
    }

    public function merge(Request $request)
    {
        $request->validate([
            'master_id' => 'required|exists:users,id',
            'secondary_id' => 'required|exists:users,id|different:master_id'
        ]);

        DB::transaction(function () use ($request) {
            $master = User::with('userDetails')->find($request->master_id);
            $secondary = User::with('userDetails')->find($request->secondary_id);

            // Merge dynamic fields
            foreach ($secondary->userDetails as $detail) {
                $existingDetail = $master->userDetails()->where('key', $detail->key)->first();
                
                if (!$existingDetail) {
                    UserDetail::create([
                        'user_id' => $master->id,
                        'key' => $detail->key,
                        'label' => $detail->label,
                        'value' => $detail->value
                    ]);
                } else {
                    if ($existingDetail->value !== $detail->value) {
                        $existingDetail->update([
                            'value' => $existingDetail->value . ' | ' . $detail->value
                        ]);
                    }
                }
            }

            // Update contacts to point to master
            Contact::where('user_id', $secondary->id)->update(['user_id' => $master->id]);
            Contact::where('contact_id', $secondary->id)->update(['contact_id' => $master->id]);

            // Mark secondary as merged (soft delete approach)
            $secondary->update(['email' => $secondary->email . '_merged_' . time()]);
        });

        return response()->json(['success' => true, 'message' => 'Users merged successfully']);
    }

    public function getContacts(User $user)
    {
        $contacts = $user->contacts()->with('contactUser.userDetails')->where('is_merged', false)->get();
        return response()->json(['contacts' => $contacts]);
    }

    public function getAvailableContacts(User $user)
    {
        $existingContactIds = $user->contacts()->pluck('contact_id')->toArray();
        $existingContactIds[] = $user->id; // Exclude the user themselves
        
        $availableUsers = User::whereNotIn('id', $existingContactIds)->get();
        return response()->json(['users' => $availableUsers]);
    }
}