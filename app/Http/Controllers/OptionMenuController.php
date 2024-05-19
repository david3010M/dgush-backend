<?php

namespace App\Http\Controllers;

use App\Models\GroupMenu;
use App\Models\OptionMenu;
use App\Utils\Constants;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OptionMenuController extends Controller
{

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

//        VALIDATE IF THE GROUP MENU EXISTS
        if (!GroupMenu::find($request->groupmenu_id)) {
            return response()->json(
                ['message' => 'Group Menu not found'], 404
            );
        }

//        Create a new Grupo Menu
        return OptionMenu::create($request->all());
    }


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

//        VALIDATE IF THE GROUP MENU EXISTS
        if (!GroupMenu::find($request->groupmenu_id)) {
            return response()->json(
                ['message' => 'Group Menu not found'], 404
            );
        }

//        Update an Option Menu
        $optionMenu->update($request->all());
        return $optionMenu;
    }


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

//        VALIDATE IF OPTIONMENU HAS ACCESSES
        if ($optionMenu->accesses()->count() > 0) {
            return response()->json(
                ['message' => 'Option Menu has accesses associated'], 409
            );
        }

//        Delete the Option Menu
        $optionMenu->delete();
        return response()->json(
            ['message' => 'Option Menu deleted successfully']
        );
    }
}
