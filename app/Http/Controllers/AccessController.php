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
     * @OA\Get(
     *     path="/api/access",
     *     tags={"Access"},
     *     summary="Get all accesses",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Returns all accesses",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Access")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return Access::all();
    }

    /**
     * @OA\Post(
     *     path="/api/access",
     *     tags={"TypeUser Accesses"},
     *     summary="Add access to typeuser",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"optionmenu_id", "typeuser_id"},
     *             @OA\Property(property="optionmenu_id", type="string", example="1,2,3"),
     *             @OA\Property(property="typeuser_id", type="integer", example="1"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Optionmenus successfully added to the typeuser",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Optionmenus 1, 2, 3 successfully added to the typeuser")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="The access already exists",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The access already exists")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Typeuser or optionmenu not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Typeuser not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="The optionmenu_id cannot have a comma at the end",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The optionmenu_id cannot have a comma at the end")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
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
     * @OA\Get(
     *     path="/api/access/{id}",
     *     tags={"TypeUser Accesses"},
     *     summary="Get access",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Access ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Returns the accesses of the typeuser",
     *         @OA\JsonContent(
     *             @OA\Property(property="accesses", type="array", @OA\Items(ref="#/components/schemas/Access"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Typeuser not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Typeuser not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
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

    /**
     * @OA\Put(
     *     path="/api/access/{id}",
     *     tags={"TypeUser Accesses"},
     *     summary="Update access",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Access ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"optionmenu_id"},
     *             @OA\Property(property="optionmenu_id", type="string", example="1,2,3"),
     *             @OA\Property(property="typeuser_id", type="integer", example="1"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Optionmenus successfully updated to the typeuser",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Optionmenus 1, 2, 3 successfully updated to the typeuser")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="You cannot update the admin access",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You cannot update the admin access")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Optionmenu or typeuser not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Optionmenu not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="The optionmenu_id cannot have a comma at the end",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The optionmenu_id cannot have a comma at the end")
     *         )
     *     )
     * )
     */
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


    /**
     * @OA\Delete(
     *     path="/api/access/{id}",
     *     tags={"Access"},
     *     summary="Delete access",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Access ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Access deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Access deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="You cannot delete the admin access",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You cannot delete the admin access")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Access not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Access not found")
     *         )
     *     )
     * )
     */
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
