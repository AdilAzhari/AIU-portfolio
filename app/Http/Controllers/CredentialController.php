<?php

namespace App\Http\Controllers;

use App\Models\Credential;
use Illuminate\Http\Request;

class CredentialController extends Controller
{
    // Issuer: create credential for a student
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'evidence_id' => ['nullable', 'exists:evidences,id'],
        ]);

        $credential = Credential::create([
            'student_id' => $request->student_id,
            'issuer_id' => auth()->id(),
            'evidence_id' => $request->evidence_id,
            'title' => strip_tags($request->title),
            'description' => strip_tags($request->description),
        ]);

        $credential->log('credential_created', [
            'student_id' => $credential->student_id,
        ]);

        $credential->markIssued();
        $credential->log('credential_issued');

        $credential->revoke($request->reason);
        $credential->log('credential_revoked', ['reason' => $request->reason]);

        return back()->with('status', 'Credential created and pending approval.');
    }

    // Issuer: mark credential as "issued"
    public function issue(Credential $credential)
    {
        // RBAC: only issuer of this credential OR admin
        if (auth()->id() !== $credential->issuer_id && ! auth()->user()->isRole('admin')) {
            abort(403, 'Unauthorized');
        }

        $credential->markIssued();

        return back()->with('status', 'Credential successfully issued.');
    }

    // Issuer: revoke credential
    public function revoke(Request $request, Credential $credential)
    {
        if (auth()->id() !== $credential->issuer_id && ! auth()->user()->isRole('admin')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $credential->revoke($request->reason);

        return back()->with('status', 'Credential revoked.');
    }
}
