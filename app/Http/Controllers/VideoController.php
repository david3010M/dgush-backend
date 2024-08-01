<?php

namespace App\Http\Controllers;

use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Http\Requests\UpdateVideoRequest;

class VideoController extends Controller
{
    public function index()
    {
        $video = Video::find(1);
        return response()->json(new VideoResource($video));
    }

    public function update(UpdateVideoRequest $request)
    {
        $id = 1;
        $video = Video::find($id);
        $video->update($request->validated());
        return response()->json(new VideoResource($video));
    }
}
