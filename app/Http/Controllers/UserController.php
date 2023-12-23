<?php

namespace App\Http\Controllers;

use App\Helper\FileHelper;
use App\Http\Middleware\UserMiddleware;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\Admin;
use App\Models\SettingBroadcast;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Nette\Utils\Random;

class UserController extends Controller
{
    protected function get_user()
    {
        $user = User::where('user_id', auth()->user()->user_id)->first();

        return $user;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = $this->get_user();

        return new UserResource($user);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        $data = $request->validated();

        $email = 'email@' . Random::generate(3, '0-9') . 'gmail.com';
        $user_id = 'user_' . Random::generate(length: 20);
        $cek = User::where('email', $email)->count();
        do {
            $email = 'email' . Random::generate(3, '0-9') . '@gmail.com';
        } while ($cek > 0);

        $tokens = SettingBroadcast::all();
        if (count($tokens) > 0) {
            foreach ($tokens as $token) {
                try {
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://api.fonnte.com/send',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => array(
                            'target' => $data['phone'],
                            'message' => 'Thank you for registering, here is the link to activate the account

' . env('APP_URL') . '/user/activate/' . $user_id,
                        ),
                        CURLOPT_HTTPHEADER => array(
                            'Authorization: ' . $token->token
                        ),
                    ));

                    curl_exec($curl);
                    if (curl_errno($curl)) {
                        $error_msg = curl_error($curl);
                    }
                    curl_close($curl);

                    if (isset($error_msg)) {
                        return $error_msg;
                    }
                } catch (\Throwable $th) {
                    throw new HttpResponseException(response([
                        'errors' => $th->getMessage()
                    ]));
                }
            }
        } else {
            return response()->json([
                'errors' => [
                    'Token not found'
                ]
            ]);
        }

        $user = new User;
        $user->user_id = $user_id;
        $user->name = 'name_' . Random::generate(length: 5);
        $user->email = $email;
        $user->phone = $data['phone'];
        $user->password = Hash::make('password');
        $user->user_is_active = 'no';
        $user->save();

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     */
    public function activate(string $id)
    {
        $user = User::where('user_id', $id)->first();

        if (!$user) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ], 404));
        }

        $user->user_is_active = 'yes';
        $user->save();

        return response()->json([
            'status' => true
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request)
    {
        $data = $request->validated();

        $user = $this->get_user();

        if (isset($data['name'])) {
            $user->name = $data['name'];
        }
        if (isset($data['email'])) {
            $user->email = $data['email'];
        }
        if (isset($data['phone'])) {
            $user->phone = $data['phone'];
        }
        if (isset($data['address'])) {
            $user->address = $data['address'];
        }
        if (isset($data['password'])) {
            $user->password = $data['password'];
        }
        if (isset($data['image'])) {
            if ($user->image) {
                FileHelper::instance()->delete($user->image);
            }
            FileHelper::instance()->upload($data['image'], 'user');
        }
        $user->save();

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        $user = $this->get_user();

        $user->token = null;

        return response()->json([
            'status' => true
        ]);
    }
}
