<?php

namespace App\Http\Controllers;

use App\Http\Requests\PackageStoreRequest;
use App\Http\Requests\PackageUpdateRequest;
use App\Http\Resources\PackageResource;
use App\Models\Admin;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Nette\Utils\Random;

class PackageController extends Controller
{
    /**
     * Get package by id
     */
    protected function get_package($id)
    {
        $admin = Admin::where('admin_id', auth()->user()->admin_id)->first();

        if ($admin) {
            $package = Package::where('package_id', $id)
                ->where('admin_id', auth()->user()->admin_id)->first();
        } else {
            $package = Package::where('package_id', $id)
                ->where('user_id', auth()->user()->user_id)->first();
        }

        if (!$package) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ], 404));
        }

        return $package;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $per_page = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $admin = Admin::where('admin_id', auth()->user()->admin_id)->first();

        if ($admin) {
            $package = Package::query()->where('admin_id', auth()->user()->admin_id)
                ->paginate(perPage: $per_page, page: $page);
        } else {
            $package = Package::query()->where('user_id', auth()->user()->user_id)
                ->orWhere('admin_id', 'like', "%adm_%")
                ->paginate(perPage: $per_page, page: $page);
        }

        return PackageResource::collection($package);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PackageStoreRequest $request)
    {
        $data = $request->validated();

        $admin = Admin::where('admin_id', auth()->user()->admin_id)->first();
        $package = new Package;
        $package->package_id = 'pkg_' . Random::generate();
        if ($admin) {
            $package->name_package = $data['name_package'];
            $package->weather_package = implode(',', $data['weather_package']);
            $package->temperature_package = $data['temperature_package'];
            $package->windspeed_package = $data['windspeed_package'];
            $package->relative_humidity_package = $data['relative_humidity_package'];
            $package->precipitation_package = $data['precipitation_package'];
            $package->total_hour_package = $data['total_hour_package'];
            $package->user_id = null;
            $package->admin_id = auth()->user()->admin_id;
        } else {
            $package->name_package = $data['name_package'];
            $package->weather_package = implode(',', $data['weather_package']);
            $package->temperature_package = $data['temperature_package'];
            $package->windspeed_package = $data['windspeed_package'];
            $package->relative_humidity_package = $data['relative_humidity_package'];
            $package->precipitation_package = $data['precipitation_package'];
            $package->total_hour_package = $data['total_hour_package'];
            $package->user_id = auth()->user()->user_id;
            $package->admin_id = null;
        }
        $package->save();

        return new PackageResource($package);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $package = $this->get_package($id);

        return new PackageResource($package);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PackageUpdateRequest $request, string $id)
    {
        $data = $request->validated();

        $package = $this->get_package($id);

        if (isset($data['name_package'])) {
            $package->name_package = $data['name_package'];
        }
        if (isset($data['weather_package'])) {
            $package->weather_package = implode(',', $data['weather_package']);
        }
        if (isset($data['temperature_package'])) {
            $package->temperature_package = $data['temperature_package'];
        }
        if (isset($data['windspeed_package'])) {
            $package->windspeed_package = $data['windspeed_package'];
        }
        if (isset($data['total_hour_package'])) {
            $package->total_hour_package = $data['total_hour_package'];
        }
        $package->save();

        return new PackageResource($package);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $package = $this->get_package($id);
        $package->delete();

        return response()->json([
            'status' => true
        ])->setStatusCode(200);
    }
}
