<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\TokenStoreRequest;
use App\Http\Requests\Admin\TokenUpdateRequest;
use App\Http\Resources\TokenResource;
use App\Models\SettingBroadcast;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Nette\Utils\Random;

class SettingTokenController extends Controller
{
    protected function get_token($id)
    {
        $token = SettingBroadcast::where('setting_id', $id)->first();

        if (!$token) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ], 404));
        }

        return $token;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $token = SettingBroadcast::all();

        return TokenResource::collection($token);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TokenStoreRequest $request)
    {
        $data = $request->validated();

        $token = new SettingBroadcast;
        $token->setting_id = 'setting_' . Random::generate();
        $token->token = $data['token'];
        $token->name = $data['name'];
        $token->setting_number = $data['setting_number'];
        $token->save();

        return new TokenResource($token);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $token = $this->get_token($id);

        return new TokenResource($token);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TokenUpdateRequest $request, string $id)
    {
        $token = $this->get_token($id);
        $data = $request->validated();

        if (isset($data['token'])) {
            $token->token = $data['token'];
        }
        if (isset($data['setting_number'])) {
            $token->setting_number = $data['setting_number'];
        }
        if (isset($data['name'])) {
            $token->name = $data['name'];
        }
        $token->save();

        return new TokenResource($token);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $token = $this->get_token($id);
        $token->delete();

        return response()->json([
            'status' => true
        ]);
    }
}
