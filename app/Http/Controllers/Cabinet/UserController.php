<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $users = User::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();
        $tenant = auth()->user()->tenant;

        return view('cabinet.users.index', compact('users', 'tenant'));
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()->tenant;

        // Vérifier le quota utilisateurs du plan
        $currentCount = User::where('tenant_id', $tenant->id)->count();
        if ($currentCount >= $tenant->quota_users) {
            return back()->withErrors([
                'quota' => "Quota utilisateurs atteint ({$tenant->quota_users} max). Passez à un plan supérieur.",
            ]);
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'role'     => ['required', Rule::in(['admin', 'comptable', 'auditeur'])],
            'password' => 'required|min:8|confirmed',
        ]);

        User::create([
            'tenant_id' => $tenant->id,
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'role'      => $validated['role'],
            'password'  => Hash::make($validated['password']),
            'actif'     => true,
        ]);

        return back()->with('succes', "Utilisateur {$validated['name']} créé avec succès.");
    }

    public function update(Request $request, User $user)
    {
        // Isolation tenant — ne peut modifier que les users de son cabinet
        abort_unless($user->tenant_id === auth()->user()->tenant_id, 403);

        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'role'     => ['required', Rule::in(['admin', 'comptable', 'auditeur'])],
            'actif'    => 'boolean',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $data = [
            'name'  => $validated['name'],
            'role'  => $validated['role'],
            'actif' => $request->boolean('actif'),
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return back()->with('succes', 'Utilisateur mis à jour.');
    }

    public function destroy(User $user)
    {
        abort_unless($user->tenant_id === auth()->user()->tenant_id, 403);
        // Ne pas se supprimer soi-même
        abort_if($user->id === auth()->id(), 422);

        $user->delete();

        return back()->with('succes', 'Utilisateur supprimé.');
    }
}
