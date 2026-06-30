<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\LandlordAppliedMail;

class RoleRegisterController extends Controller
{
    public function choose()
    {
        return view('auth.register-choose');
    }

    /* =========================
       STUDENT
    ========================= */
    public function studentForm()
    {
        return view('auth.register-student');
    }

    public function studentStore(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'student_id' => ['required', 'string', 'max:50'],
            'programme' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'age' => ['nullable', 'integer', 'min:16', 'max:80'],
            'gender' => ['nullable', 'in:male,female,other'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'postcode' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'student',

            'student_id' => $data['student_id'],
            'programme' => $data['programme'],
            'phone' => $data['phone'] ?? null,
            'age' => $data['age'] ?? null,
            'gender' => $data['gender'] ?? null,
            'address_line1' => $data['address_line1'] ?? null,
            'address_line2' => $data['address_line2'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'postcode' => $data['postcode'] ?? null,

            'landlord_status' => null,
            'landlord_verified_at' => null,
            'landlord_rejected_reason' => null,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('student.dashboard');
    }

    /* =========================
       LANDLORD (PENDING VERIFICATION)
    ========================= */
    public function landlordForm()
    {
        return view('auth.register-landlord');
    }

    public function landlordStore(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'company_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'verification_document' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        if (!$request->hasFile('verification_document')) {
            return back()
                ->withInput()
                ->withErrors(['verification_document' => 'Please choose a verification document.']);
        }

        $uploadedFile = $request->file('verification_document');

        if (!$uploadedFile || !$uploadedFile->isValid()) {
            return back()
                ->withInput()
                ->withErrors(['verification_document' => 'The uploaded document is invalid. Please try again.']);
        }

        $documentPath = $uploadedFile->store('landlord-verification-documents', 'public');
        $documentType = strtolower($uploadedFile->getClientOriginalExtension());

        Log::info('Landlord verification upload result', [
            'email' => $data['email'],
            'document_path' => $documentPath,
            'document_type' => $documentType,
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'landlord',

            'company_name' => $data['company_name'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,

            'verification_document_path' => $documentPath,
            'verification_document_type' => $documentType,

            'landlord_status' => 'pending',
            'landlord_verified_at' => null,
            'landlord_rejected_reason' => null,
        ]);

        event(new Registered($user));
        Auth::login($user);

        try {
            Mail::to($user->email)->send(new LandlordAppliedMail($user));
        } catch (\Throwable $e) {
            Log::error('Landlord apply mail failed: ' . $e->getMessage());
        }

        return redirect('/landlord/pending')
            ->with('success', 'Thanks for applying! Your landlord account is pending admin approval.');
    }
}