<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use App\Http\Requests\ImageRequest;
use App\Http\Resources\ImageResource;

class ImageController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return ImageResource::collection(Image::all());
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\ImageRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(ImageRequest $request)
	{
		$image = Image::create($request->all());
		return new ImageResource($image);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Image  $image
	 * @return \Illuminate\Http\Response
	 */
	public function show(Image $image)
	{
		return new ImageResource($image);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\ImageRequest  $request
	 * @param  \App\Models\Image  $image
	 * @return \Illuminate\Http\Response
	 */
	public function update(ImageRequest $request, Image $image)
	{
		$image->update($request->all());
		return new ImageResource($image);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Image  $image
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Image $image)
	{
		$val = $image->delete();
		return response($val, 204);
	}
}
