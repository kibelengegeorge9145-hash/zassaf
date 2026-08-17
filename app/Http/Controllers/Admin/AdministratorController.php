<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdministratorController extends Controller
{
    public function index()
    {
        $administrators = User::orderBy('role')->orderBy('name')->get();

        return view('admin.administrators.index', compact('administrators'));
    }

    public function create()
    {
        return view('admin.administrators.form', ['user' => new User()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, requirePassword: true);

        $user = User::create($data);

        AuditLog::log(
            AuditLog::ACTION_ADMIN_CREATED,
            __('admin.audit.admin_created', ['actor' => $request->user()->name, 'name' => $user->name]),
            $user
        );

        return redirect()->route('admin.administrators.index')
            ->with('success', __('admin.administrators.created'));
    }

    public function show(User $user)
    {
        return view('admin.administrators.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.administrators.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, requirePassword: false, ignore: $user);

        $roleChanged = $user->role !== ($data['role'] ?? $user->role);

        if ($request->user()->is($user)) {
            $data['role'] = User::ROLE_SUPER_ADMIN;
            $data['is_active'] = true;
            $roleChanged = false;
        }

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        if ($roleChanged) {
            AuditLog::log(
                AuditLog::ACTION_ROLE_CHANGED,
                __('admin.audit.role_changed', [
                    'actor' => $request->user()->name,
                    'name' => $user->name,
                    'from' => __('admin.roles.' . $user->getOriginal('role')),
                    'to' => $user->role_label,
                ]),
                $user
            );
        }

        AuditLog::log(
            AuditLog::ACTION_ADMIN_UPDATED,
            __('admin.audit.admin_updated', ['actor' => $request->user()->name, 'name' => $user->name]),
            $user
        );

        return redirect()->route('admin.administrators.index')
            ->with('success', __('admin.administrators.updated'));
    }

    public function toggleActive(Request $request, User $user)
    {
        if ($request->user()->is($user)) {
            return back()->with('error', __('admin.administrators.cannot_edit_self'));
        }

        if ($user->is_active && $user->isSuperAdmin() && $this->activeSuperAdminCount() <= 1) {
            return back()->with('error', __('admin.administrators.last_admin'));
        }

        $user->update(['is_active' => ! $user->is_active]);

        AuditLog::log(
            $user->is_active ? AuditLog::ACTION_ADMIN_ACTIVATED : AuditLog::ACTION_ADMIN_DEACTIVATED,
            ($user->is_active
                ? __('admin.audit.admin_activated', ['actor' => $request->user()->name, 'name' => $user->name])
                : __('admin.audit.admin_deactivated', ['actor' => $request->user()->name, 'name' => $user->name])),
            $user
        );

        return back()->with('success', __('admin.status_changed'));
    }

    public function destroy(Request $request, User $user)
    {
        if ($request->user()->is($user)) {
            return back()->with('error', __('admin.administrators.cannot_edit_self'));
        }

        if ($user->isSuperAdmin() && $this->activeSuperAdminCount() <= 1) {
            return back()->with('error', __('admin.administrators.last_admin'));
        }

        AuditLog::log(
            AuditLog::ACTION_ADMIN_DELETED,
            __('admin.audit.admin_deleted', ['actor' => $request->user()->name, 'name' => $user->name]),
            $user
        );

        $user->delete();

        return back()->with('success', __('admin.deleted'));
    }

    private function activeSuperAdminCount(): int
    {
        return User::query()
            ->where('role', User::ROLE_SUPER_ADMIN)
            ->where('is_active', true)
            ->count();
    }

    private function validated(Request $request, bool $requirePassword, ?User $ignore = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => [
                'required', 'string', 'max:40', 'alpha_dash',
                Rule::unique('users', 'username')->ignore($ignore?->id),
            ],
            'email' => [
                'required', 'email', 'max:190',
                Rule::unique('users', 'email')->ignore($ignore?->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => [
                $requirePassword ? 'required' : 'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
            'role' => ['required', 'string', Rule::in(User::ROLES)],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['username'] = Str::lower(Str::slug($data['username'], ''));

        return $data;
    }
}
