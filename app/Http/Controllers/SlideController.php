<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Slide;
use App\Http\Resources\SlideResource;

class SlideController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $slides = Slide::all();
        return [
            'status' => 'success',
            'data'   => SlideResource::collection($slides)
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
            'title'       => 'required|string',
            'description' => 'required|string',
            'image'       => 'required|string',
            'button_text' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'error' => $validator->errors()->toJson()
                ], 400
            );
        }

        $id = Slide::create($validator->validated())->id;
        return response()->json([
            'status'  => 'success',
            'message' => 'Created slide with id '.$id
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
        $slide = Slide::findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data'   => new SlideResource($slide)
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
        $slide = Slide::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'title'       => 'nullable|string',
            'description' => 'nullable|string',
            'image'       => 'nullable|string',
            'button_text' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'error' => $validator->errors()->toJson()
                ], 400
            );
        }

        $slide->update($validator->validated());
        $slide->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Successfully updated slide'
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
        Slide::findOrFail($id)->delete();
        return response()->json([
            'status'  => 'success',
            'message' => 'Successfully deleted slide'
        ]);
    }
}
