<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Winner;
use App\Http\Resources\WinnerResource;

class WinnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $winners = Winner::all();
        return [
            'status' => 'success',
            'data'   => WinnerResource::collection($winners)
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'    => 'required|string|exists:users,id',
            'game_id'    => 'required|string|exists:games,id',
            'image'      => 'required|string',
            'amount'     => 'required|numeric|min:0',
            'time'       => 'required|date_format:H:i',
            'demo'       => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'error' => $validator->errors()->toJson()
                ], 400
            );
        }
        
        $id = Winner::create($validator->validated())->id;

        return response()->json([
            'status'  => 'success',
            'message' => 'Created winner with id '.$id
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $winner = Winner::findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data'   => new WinnerResource($winner)
        ]);
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
        $winner = Winner::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'user_id'    => 'nullable|string|exists:users,id',
            'game_id'    => 'nullable|string|exists:games,id',
            'image'      => 'nullable|string',
            'amount'     => 'nullable|numeric|min:0',
            'time'       => 'nullable|date_format:H:i',
            'demo'       => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'error' => $validator->errors()->toJson()
                ], 400
            );
        }

        $winner->update($validator->validated());
        $winner->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Successfully updated winner'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Winner::findOrFail($id)->delete();
        return response()->json([
            'status'  => 'success',
            'message' => 'Successfully deleted winner'
        ]);
    }
}
