<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use Illuminate\Http\Request;

class StudentAccountController extends Controller
{
    public function edit()
    {
        $user = auth()->user();

        $profile = StudentProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => $user->phone,
                'gender' => $user->gender,
                'address_line1' => $user->address_line1,
                'address_line2' => $user->address_line2,
                'postcode' => $user->postcode,
                'city' => $user->city,
                'state' => $user->state,
                'country' => 'Malaysia', // optional default
            ]
        );

        return view('student.profile', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'nric_passport' => ['nullable', 'string', 'max:30'], // ✅ correct name

            'date_of_birth' => ['nullable', 'date'],
            'nationality'   => ['nullable', 'string', 'max:80'],
            'race'          => ['nullable', 'string', 'max:80'],
            'religion'      => ['nullable', 'string', 'max:80'],
            'phone'         => ['nullable', 'string', 'max:30'],
            'gender'        => ['nullable', 'string', 'max:20'],

            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'postcode'      => ['nullable', 'string', 'max:20'],
            'city'          => ['nullable', 'string', 'max:100'],
            'state'         => ['nullable', 'string', 'max:100'],
            'country'       => ['nullable', 'string', 'max:100'],

            'emergency_name'         => ['nullable', 'string', 'max:120'],
            'emergency_phone'        => ['nullable', 'string', 'max:30'],
            'emergency_relationship' => ['nullable', 'string', 'max:60'],
        ]);

        $profile = StudentProfile::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        // keep users table synced (optional)
        $user->update([
            'phone'         => $profile->phone ?? $user->phone,
            'gender'        => $profile->gender ?? $user->gender,
            'address_line1' => $profile->address_line1 ?? $user->address_line1,
            'address_line2' => $profile->address_line2 ?? $user->address_line2,
            'postcode'      => $profile->postcode ?? $user->postcode,
            'city'          => $profile->city ?? $user->city,
            'state'         => $profile->state ?? $user->state,
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }
}