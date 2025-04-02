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
        'name',
        'url',
        'product_id',
        'subcategory_id',
        'color_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
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
