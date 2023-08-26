<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

use App\Models\User;
use App\Http\Resources\CommonClientResource;
use App\Http\Resources\DetailClientResource;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user()->cannot('viewAny', User::class)) {
            abort(response()->json(
                [
                    'status' => 'error',
                    'message' => 'You are not authorized',
                ], 403)
            );
        }
        $users = User::where('role', '=', 'client')->get();
        return [
            'status' => 'success',
            'data'   => CommonClientResource::collection($users)
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        if ($request->user()->cannot('view', $user)) {
            abort(response()->json(
                [
                    'status' => 'error',
                    'message' => 'You are not authorized',
                ], 403)
            );
        }
        return new DetailClientResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($request->user()->cannot('update', $user)) {
            abort(response()->json(
                [
                    'status' => 'error',
                    'message' => 'You are not authorized',
                ], 403)
            );
        }

        $validator = Validator::make($request->all(), [
            'login'      => 'nullable|string',
            'role'       => ['nullable', Rule::in(config('enums.roles'))],
            'birth_date' => 'nullable|date',
            'first_name' => 'nullable|string',
            'last_name'  => 'nullable|string',
            'gender'     => ['nullable', Rule::in(['M', 'F'])],
            'patronymic' => 'nullable|string',
            'phone'      => 'nullable|string',
            'email'      => 'nullable|email|unique:users',
            'address'    => 'nullable|string',
            'password'   => [
                'nullable',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'error' => $validator->errors()->toJson()
                ], 400
            );
        }
        
        $data = $validator->validated();
        if (array_key_exists('password', $data)) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        $user->save();

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Successfully changed client info'
            ], 200
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($request->user()->cannot('delete', $user)) {
            abort(response()->json(
                [
                    'status' => 'error',
                    'message' => 'You are not authorized',
                ], 403)
            );
        }
        $user->delete();
        return [
            "message" => "Successfully deleted user"
        ];
    }
}
