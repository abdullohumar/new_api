<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::when(request()->search, function($roles) {
            $roles = $roles->where('name', 'like', '%'. request()->search . '%');
        })->with('permissions')->latest()->paginate(5);

        $roles->appends(['search' => request()->search]);

        return new RoleResource(true, 'List Data Roles', $roles);   
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'permissions' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create role
        $role = Role::create([
            'name' => $request->name
        ]);

        $role->givePermissionTo($request->permissions);

        if($role) {
            return new RoleResource(true, 'Data Role Created', $role);
        }
        
        return new RoleResource(false, 'Data Role Failed to Created', null); 
        
    }

    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        if($role) {
            return new RoleResource(true, 'Data Role Found', $role);
        }
        return new RoleResource(false, 'Data Role Not Found', null);
    }

    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'permissions' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $role->update([
            'name' => $request->name
        ]);

        $role->syncPermissions($request->permissions);

        if($role) {
            return new RoleResource(true, 'Data Role Updated', $role);
        }

        return new RoleResource(false, 'Data Role Failed to Updated', null);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        if($role) {
            $role->delete();
            return new RoleResource(true, 'Data Role Deleted', $role);
        }
        return new RoleResource(false, 'Data Role Not Found', null);
    }

    public function all()
    {
        $roles = Role::latest()->get();
        
        return new RoleResource(true, 'List Data Roles', $roles);
    }
        
}
