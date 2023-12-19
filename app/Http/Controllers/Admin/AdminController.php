<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Http\Requests\Admin\AdminUpdateRequest;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use function PHPUnit\Framework\returnSelf;

class AdminController extends Controller
{
    public function login(AdminLoginRequest $request)
    {
        $data = $request->validated();

        $admin = Admin::where('email', $data['email'])->first();
        if (!$admin || !Hash::check($data['password'], $admin->password)) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'email or password wrong'
                    ]
                ]
            ], 401));
        }

        $admin->token = Str::uuid()->toString();
        $admin->save();

        return new AdminResource($admin);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admin = Admin::where('admin_id', auth()->user()->admin_id)->first();

        return new AdminResource($admin);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminUpdateRequest $request)
    {
        $data = $request->validated();

        $admin  = Admin::where('admin_id', auth()->user()->admin_id)->first();
        if (isset($data['name'])) {
            $admin->name = $data['name'];
        }
        if (isset($data['email'])) {
            $admin->email = $data['email'];
        }
        if (isset($data['phone'])) {
            $admin->phone = $data['phone'];
        }
        if (isset($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }
        $admin->save();

        return new AdminResource($admin);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function logout()
    {
        $admin = Admin::where('admin_id', auth()->user()->admin_id)->first();
        $admin->token = null;
        $admin->save();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }
}
