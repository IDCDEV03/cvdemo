<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    // role ที่มีตาราง detail แยก ผูกกับ user (company_details, supply_datas, inspector_datas)
    // ห้ามสร้างใหม่จากหน้านี้ และห้ามเปลี่ยน role ออกจากกลุ่มนี้ผ่านหน้านี้
    private const LINKED_ROLES = [Role::Company, Role::Supply, Role::Inspector];

    // role ที่จัดการได้เต็มรูปแบบ (เพิ่ม/แก้ไข/ลบ/เปลี่ยน role ภายในกลุ่ม) จากหน้านี้
    private const MANAGEABLE_ROLES = [Role::Agency, Role::Manager, Role::User, Role::Staff];

    // role ที่ยังไม่ได้ใช้งานจริงในระบบ ซ่อนจากหน้าจัดการผู้ใช้งานไปก่อน
    private const HIDDEN_ROLES = [Role::Agency, Role::Manager, Role::User];

    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    private static function hiddenRoleValues(): array
    {
        return array_map(fn ($role) => $role->value, self::HIDDEN_ROLES);
    }

    public function index(Request $request)
    {
        $roleFilter = $request->query('role');

        $query = User::where('role', '!=', Role::Admin->value)
            ->whereNotIn('role', self::hiddenRoleValues());

        if ($roleFilter && Role::tryFrom($roleFilter)) {
            $query->where('role', $roleFilter);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $users = $query->orderByDesc('created_at')->get();

        $roles = array_filter(Role::cases(), fn ($role) => $role !== Role::Admin && !in_array($role, self::HIDDEN_ROLES, true));

        // ชื่อบริษัทว่าจ้าง (role=company) แม็พด้วย user_id เพื่อโยงกับ users.company_code ของ role=supply
        $companyNames = User::where('role', Role::Company->value)->pluck('name', 'user_id');

        return view('pages.admin.UserManagement.index', compact('users', 'roles', 'roleFilter', 'companyNames'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        abort_if($user->role === Role::Admin, 403, 'ไม่สามารถจัดการผู้ใช้งาน role admin ได้');

        $roleLocked = in_array($user->role, self::LINKED_ROLES, true);

        $roles = $roleLocked
            ? [$user->role]
            : array_filter(self::MANAGEABLE_ROLES, fn ($role) => !in_array($role, self::HIDDEN_ROLES, true));

        return view('pages.admin.UserManagement.edit', compact('user', 'roles', 'roleLocked'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        abort_if($user->role === Role::Admin, 403, 'ไม่สามารถจัดการผู้ใช้งาน role admin ได้');

        $roleLocked = in_array($user->role, self::LINKED_ROLES, true);
        $manageableValues = array_map(fn ($role) => $role->value, self::MANAGEABLE_ROLES);

        $rules = [
            'prefix' => 'nullable|string|max:20',
            'name' => 'required|string|max:200',
            'lastname' => 'nullable|string|max:200',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'username' => 'nullable|string|max:30|unique:users,username,' . $user->id,
            'user_phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:4',
            'user_status' => 'required|in:1,2',
        ];

        if (!$roleLocked) {
            $rules['role'] = 'required|in:' . implode(',', $manageableValues);
        }

        $data = $request->validate($rules);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($roleLocked) {
            unset($data['role']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'แก้ไขข้อมูลผู้ใช้งานเรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        abort_if($user->role === Role::Admin, 403, 'ไม่สามารถลบผู้ใช้งาน role admin ได้');

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'ลบผู้ใช้งานเรียบร้อยแล้ว');
    }

    public function toggleStatus($id, $status)
    {
        $user = User::findOrFail($id);

        abort_if($user->role === Role::Admin, 403, 'ไม่สามารถจัดการผู้ใช้งาน role admin ได้');
        abort_unless(in_array($status, ['1', '2'], true), 400);

        $user->update(['user_status' => $status]);

        return redirect()->route('admin.users.index')->with('success', 'บันทึกสถานะเรียบร้อยแล้ว');
    }
}
