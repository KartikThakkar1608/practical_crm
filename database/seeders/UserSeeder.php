<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Contact;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'phone' => '+1234567890',
            'gender' => 'male'
        ]);

        UserDetail::create([
            'user_id' => $user1->id,
            'key' => 'birth_date',
            'label' => 'Birth Date',
            'value' => '1990-01-15'
        ]);

        UserDetail::create([
            'user_id' => $user1->id,
            'key' => 'company',
            'label' => 'Company',
            'value' => 'Tech Corp'
        ]);

        $user2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password123'),
            'phone' => '+1987654321',
            'gender' => 'female'
        ]);

        UserDetail::create([
            'user_id' => $user2->id,
            'key' => 'birth_date',
            'label' => 'Birth Date',
            'value' => '1992-05-20'
        ]);

        UserDetail::create([
            'user_id' => $user2->id,
            'key' => 'address',
            'label' => 'Address',
            'value' => '123 Main St, City'
        ]);

        $user3 = User::create([
            'name' => 'Bob Johnson',
            'email' => 'bob@example.com',
            'password' => Hash::make('password123'),
            'phone' => '+1122334455',
            'gender' => 'male'
        ]);

        UserDetail::create([
            'user_id' => $user3->id,
            'key' => 'department',
            'label' => 'Department',
            'value' => 'Marketing'
        ]);

        // Create contacts
        Contact::create([
            'user_id' => $user1->id,
            'contact_id' => $user2->id
        ]);

        Contact::create([
            'user_id' => $user1->id,
            'contact_id' => $user3->id
        ]);
    }
}