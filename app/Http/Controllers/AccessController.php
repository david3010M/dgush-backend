<?php

namespace App\Http\Controllers;

use App\Models\Access;
use App\Models\OptionMenu;
use App\Models\TypeUser;
use App\Rules\CompositeForeignKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Access::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate data
        $validation = $this->validateAccess($request);

        if ($validation->getStatusCode() !== 200) {
            return $validation;
        }

        // Store in database
        return Access::create($request->all());

    }

    function validateAccess(Request $request): JsonResponse
    {
        $optionmenu_id = $request->input('optionmenu_id');
        $typeuser_id = $request->input('typeuser_id');

        // Validate data
        $request->validate([
            'optionmenu_id' => 'required|integer',
            'typeuser_id' => 'required|integer',
        ]);

        if (Access::where('optionmenu_id', $optionmenu_id)
            ->where('typeuser_id', $typeuser_id)
            ->exists()) {
            return response()->json(['message' => 'The access already exists.'], 400);
        }

//        Find the optionmenu_id
        $optionmenu = OptionMenu::find($request->optionmenu_id);
        if (!$optionmenu) {
            return response()->json(['message' => 'Optionmenu not found'], 404);
        }

        // Find the typeuser_id
        $typeuser = TypeUser::find($request->typeuser_id);
        if (!$typeuser) {
            return response()->json(['message' => 'Typeuser not found'], 404);
        }

        return response()->json(1);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        // Find a user by ID
        $access = Access::find($id);

        // If the user is not found, return a 404 response
        if (!$access) {
            return response()->json(['message' => 'Access not found'], 404);
        }

        return $access;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Access $access)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        // Find the access
        $access = Access::find($id);

        // If the access is not found, return a 404 response
        if (!$access) {
            return response()->json(['message' => 'Access not found'], 404);
        }

        // Validate data
        $validation = $this->validateAccess($request);

        if ($validation->getStatusCode() !== 200) {
            return $validation;
        }

        // Update the access
        $access->update($request->all());

        return $access;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
//        Find the access
        $access = Access::find($id);

        // If the access is not found, return a 404 response
        if (!$access) {
            return response()->json(['message' => 'Access not found'], 404);
        }

        // Delete the access
        $access->delete();

        return response()->json(['message' => 'Access deleted successfully']);
    }
}
