<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\LandlordProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class LandlordProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        abort_unless($user && $user->role === 'landlord', 403);

        $profile = LandlordProfile::firstOrCreate(
            ['user_id' => $user->id],
            $this->defaultProfileData($user)
        );

        // If profile already exists but some fields are empty, fill them once from users table
        $updates = [];

        foreach ($this->defaultProfileData($user) as $key => $value) {
            if (
                in_array($key, [
                    'phone',
                    'company_name',
                    'business_registration_no',
                    'address_line1',
                    'address_line2',
                    'postcode',
                    'city',
                    'state',
                    'country'
                ], true)
                && empty($profile->{$key})
                && !empty($value)
            ) {
                $updates[$key] = $value;
            }
        }

        if (!empty($updates)) {
            $profile->update($updates);
            $profile->refresh();
        }

        return view('landlord.profile.edit', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        abort_unless($user && $user->role === 'landlord', 403);

        $validated = $request->validate([
            'phone' => ['nullable', 'string', 'max:30'],
            'nric' => ['nullable', 'string', 'max:50'],
            'gender' => ['nullable', 'in:Male,Female,Other'],
            'date_of_birth' => ['nullable', 'date'],

            'company_name' => ['nullable', 'string', 'max:255'],
            'business_registration_no' => ['nullable', 'string', 'max:100'],

            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'postcode' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
        ]);

        $profile = LandlordProfile::firstOrCreate(
            ['user_id' => $user->id],
            $this->defaultProfileData($user)
        );

        $profile->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    private function generateAccountId(int $userId): string
    {
        return 'L-' . str_pad((string) $userId, 6, '0', STR_PAD_LEFT);
    }

    private function defaultProfileData($user): array
    {
        return [
            'account_id' => $this->generateAccountId($user->id),

            'phone' => $this->getUserColumn($user, ['phone', 'contact_number']),
            'company_name' => $this->getUserColumn($user, ['company_name', 'business_name']),
            'business_registration_no' => $this->getUserColumn($user, ['business_registration_no', 'company_registration_no', 'ssm_no']),

            'address_line1' => $this->getUserColumn($user, ['address_line1', 'address']),
            'address_line2' => $this->getUserColumn($user, ['address_line2']),
            'postcode' => $this->getUserColumn($user, ['postcode']),
            'city' => $this->getUserColumn($user, ['city']),
            'state' => $this->getUserColumn($user, ['state']),
            'country' => $this->getUserColumn($user, ['country']) ?: 'Malaysia',
        ];
    }

    private function getUserColumn($user, array $possibleColumns): ?string
    {
        foreach ($possibleColumns as $column) {
            if (Schema::hasColumn('users', $column)) {
                $value = $user->{$column} ?? null;
                if (!empty($value)) {
                    return $value;
                }
            }
        }

        return null;
    }
}