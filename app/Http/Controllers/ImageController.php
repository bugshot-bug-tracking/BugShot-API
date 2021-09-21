<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use App\Http\Requests\ImageRequest;
use App\Http\Resources\ImageResource;
use Illuminate\Support\Facades\Storage;

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
		$storagePath = "/uploads/images";

		$savedPath = $request->file->store($storagePath);

		$image = Image::create([
			"designation" => $request->file->getClientOriginalName(),
			"url" => $savedPath,
		]);

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
		$storagePath = "/uploads/images";

		$savedPath = $request->file->store($storagePath);

		Storage::delete($image->url);

		$image->update([
			"designation" => $request->file->getClientOriginalName(),
			"url" => $savedPath,
		]);

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

	/**
	 * Download the specified resource.
	 *
	 * @param  \App\Models\Image  $image
	 * @return \Illuminate\Http\Response
	 */
	public function download(Image $image)
	{
		return Storage::download($image->url, $image->designation);
	}
}
