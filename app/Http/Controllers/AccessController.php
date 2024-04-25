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

    public function index()
    {
        return Access::all();
    }

    public function store(Request $request)
    {
//        VALIDATE DATA
        $validation = $this->validateAccess($request);

//        VALIDATE THE TYPEUSER_ID AND PERMISSION_ID ARE UNIQUE BOTH
        if (Access::where('typeuser_id', $request->typeuser_id)
            ->where('optionmenu_id', $request->optionmenu_id)
            ->exists()) {
            return response()->json(['message' => 'The access already exists.'], 400);
        }

        if ($validation->getStatusCode() !== 200) {
            return $validation;
        }

//        TYPEUSER ID FROM REQUEST
        $typeuser_id = $request->input('typeuser_id');

//        FIND TYPEUSER
        $typeuser = TypeUser::find($typeuser_id);
        if (!$typeuser) {
            return response()->json(['message' => 'Typeuser not found'], 404);
        }

//        CREATE OPTIONMENU FROM A STRING OF OPTIONMENUS WITH COMMA
        $optionmenus = explode(',', $request->input('optionmenu_id'));

        foreach ($optionmenus as $optionmenu) {
            $access = new Access();
            $access->typeuser_id = $typeuser_id;
            $access->optionmenu_id = $optionmenu;
            $access->save();
        }

        $optionmenusAdded = implode(', ', $optionmenus);

        return response()->json(['message' => 'Optionmenus ' . $optionmenusAdded . ' successfully added to the typeuser']);

    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
//        FIND TYPEUSER
        $typeuser = TypeUser::find($id);

//        ERROR MESSAGE
        if (!$typeuser) {
            return response()->json(['message' => 'Typeuser not found'], 404);
        }

//        RETURN ACCESS
        return response()->json(
            ['accesses' => $typeuser->getAccess($id)]
        );

    }

    public function update(Request $request, int $id)
    {
//        NOT UPDATE ADMIN ACCESS
        if ($id == 1) {
            return response()->json(['message' => 'You cannot update the admin access'], 400);
        }

//        VALIDATE DATA
        $validation = $this->validateAccess($request);

        if ($validation->getStatusCode() !== 200) {
            return $validation;
        }

//        TYPEUSER ID FROM REQUEST
        $typeuser_id = $id;

//        DELETE ACCESS
        Access::where('typeuser_id', $typeuser_id)->delete();

//        UPDATE ACCESS FROM A STRING OF ACCESS WITH COMMA
        $optionmenus = explode(',', $request->input('optionmenu_id'));

        foreach ($optionmenus as $optionmenu) {
            $access = new Access();
            $access->typeuser_id = $typeuser_id;
            $access->optionmenu_id = $optionmenu;
            $access->save();
        }

        $optionmenusAdded = implode(', ', $optionmenus);

        return response()->json(['message' => 'Optionmenus ' . $optionmenusAdded . ' successfully updated to the typeuser']);
    }


    public function destroy(int $id)
    {
//        FIND ACCESS
        $access = Access::find($id);

//        ERROR MESSAGE
        if (!$access) {
            return response()->json(['message' => 'Access not found'], 404);
        }

//        NOT DELETE ADMIN ACCESS
        if ($access->typeuser_id == 1) {
            return response()->json(['message' => 'You cannot delete the admin access'], 400);
        }

//        DELETE ACCESS
        $access->delete();
        return response()->json(['message' => 'Access deleted successfully']);
    }


    function validateAccess(Request $request): JsonResponse
    {
//        VALIDAR QUE LA VARIABLE OPTIONMENU_ID NO TENGA COMA AL FINAL
        if (substr($request->optionmenu_id, -1) == ',') {
            return response()->json(['message' => 'The optionmenu_id cannot have a comma at the end.'], 400);
        }
//        VALIDAR QUE LA VARIABLE OPTIONMENU_ID NO TENGA COMA AL INICIO
        if (substr($request->optionmenu_id, 0, 1) == ',') {
            return response()->json(['message' => 'The optionmenu_id cannot have a comma at the beginning.'], 400);
        }

        // Validate data
        $request->validate([
            'optionmenu_id' => 'required|string',
            'typeuser_id' => 'required|integer',
        ]);

//        VALIDATE EACH OPTIONMENU_ID IN THE VARIABLE WHICH IS A STRING OF OPTIONMENUS SEPARATED BY COMMA
        $optionmenus = explode(',', $request->input('optionmenu_id'));

        foreach ($optionmenus as $optionmenu) {
            $validationOptionMenu = $this->validateOptionMenu($optionmenu);
            if ($validationOptionMenu->getStatusCode() !== 200) {
                return $validationOptionMenu;
            }
        }

//        FIND THE TYPEUSER_ID
        $typeuser = TypeUser::find($request->typeuser_id);
        if (!$typeuser) {
            return response()->json(['message' => 'Typeuser not found'], 404);
        }

        return response()->json(1);
    }

    function validateOptionMenu($optionmenu): JsonResponse
    {
//        VALIDATE  THE VARIABLE OPTIONMENU IS INTEGER
        if (!is_numeric($optionmenu)) {
            return response()->json(['error' => 'Optionmenu ' . $optionmenu . ' is not a number'], 404);
        }

//        VALIDATE THE VARIABLE OPTIONMENU IS REQUIRED
        if (!$optionmenu) {
            return response()->json(['error' => 'Optionmenu is required'], 404);
        }

//        VALIDATE OPTIONMENU REQUIRED AND INTEGER IF NOT RETURN 404 WITH THE OPTIONMENU ENTRY
        $optionmenu_id = OptionMenu::find($optionmenu);
        if (!$optionmenu_id) {
            return response()->json(['error' => 'Optionmenu ' . $optionmenu . ' not found'], 404);
        }

        return response()->json(1);
    }
}
