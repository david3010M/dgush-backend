<?php

namespace App\Http\Controllers;

use App\Models\OptionMenu;
use App\Utils\Constants;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OptionMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
//        return OptionMenu::paginate(Constants::PAGINATION);s
        return OptionMenu::all();
    }

    public function store(Request $request): OptionMenu|JsonResponse
    {
//        Validate data
        $request->validate([
            'name' => 'required|string|unique:optionmenu',
            'route' => 'required|string|unique:optionmenu',
            'order' => 'required|integer|unique:optionmenu',
            'icon' => 'required|string',
            'groupmenu_id' => 'required|integer',
        ]);

//        Create a new Grupo Menu
        return OptionMenu::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): OptionMenu|JsonResponse
    {
        $optionMenu = OptionMenu::find($id);

        if (!$optionMenu) {
            return response()->json(
                ['message' => 'Option Menu not found'], 404
            );
        }

//        return optionMenu->load('optionMenus');
        return $optionMenu;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OptionMenu $optionMenu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
//        Find the Option Menu
        $optionMenu = OptionMenu::find($id);

//        Error when not found
        if (!$optionMenu) {
            return response()->json(
                ['message' => 'Option Menu not found'], 404
            );
        }

//        Validate data
        $request->validate([
            'name' => 'required|string|unique:optionmenu,name,' . $id . ',id',
            'route' => 'required|string|unique:optionmenu,route,' . $id . ',id',
            'order' => 'required|integer|unique:optionmenu,order,' . $id . ',id',
            'icon' => 'required|string',
            'groupmenu_id' => 'required|integer',
        ]);

//        Update an Option Menu
        $optionMenu->update($request->all());
        return $optionMenu;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
//        Find the Option Menu
        $optionMenu = OptionMenu::find($id);

//        Error when not found
        if (!$optionMenu) {
            return response()->json(
                ['message' => 'Option Menu not found'], 404
            );
        }

//        Delete the Option Menu
        $optionMenu->delete();
        return response()->json(
            ['message' => 'Option Menu deleted successfully']
        );
    }
}
