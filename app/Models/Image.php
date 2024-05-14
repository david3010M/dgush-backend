<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;

    protected $table = 'image';

    protected $fillable = [
        'url',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // ajusta los requisitos según lo necesites
        ]);
        $image = $request->file('image');

        $fileName = time() . '_' . $image->getClientOriginalName();

        Storage::disk('spaces')->put($fileName, file_get_contents($image), 'public');

        $imageUrl = Storage::disk('spaces')->url($fileName);

        return response()->json(['url' => $imageUrl], 200);
    }

    public function deleteImage(Request $request)
    {
        $request->validate([
            'imageName' => 'required|string',
        ]);

        $fileName = basename($request->imageName);

        Storage::disk('spaces')->delete($fileName);

        return response()->json(['message' => 'Image deleted'], 200);
    }
}
