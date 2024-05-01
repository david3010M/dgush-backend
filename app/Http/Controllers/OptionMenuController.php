<?php

namespace App\Http\Controllers;

use App\Models\GroupMenu;
use App\Models\OptionMenu;
use App\Utils\Constants;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OptionMenuController extends Controller
{
    /**
     * @OA\Get(
     *     path="/dgush-backend/public/api/optionmenu",
     *     summary="Get all Option Menus",
     *     tags={"OptionMenu"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Option Menus",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/OptionMenu")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     *
     */
    public function index()
    {
//        return OptionMenu::paginate(Constants::PAGINATION);s
        return OptionMenu::all();
    }


    /**
     * Create a new Option Menu
     * @OA\Post(
     *     path="/dgush-backend/public/api/optionmenu",
     *     summary="Create a new Option Menu",
     *     tags={"OptionMenu"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "route", "order", "icon", "groupmenu_id"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="route", type="string"),
     *             @OA\Property(property="order", type="integer"),
     *             @OA\Property(property="icon", type="string"),
     *             @OA\Property(property="groupmenu_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Option Menu created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/OptionMenu")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Group Menu not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Group Menu not found")
     *          )
     *     ),
     *     @OA\Response(
     *           response=422,
     *           description="Validation error",
     *           @OA\JsonContent(
     *               @OA\Property(property="message", type="string", example="The given data was invalid."),
     *               @OA\Property(property="errors", type="object",
     *                   @OA\Property(
     *                       property="name",
     *                       type="array",
     *                       @OA\Items(type="string", example="The name field is required.")
     *                   ),
     *                   @OA\Property(
     *                       property="route",
     *                       type="array",
     *                       @OA\Items(type="string", example="The route field is required.")
     *                   ),
     *                   @OA\Property(
     *                       property="order",
     *                       type="array",
     *                       @OA\Items(type="string", example="The order field is required.")
     *                   ),
     *                   @OA\Property(
     *                       property="icon",
     *                       type="array",
     *                       @OA\Items(type="string", example="The icon field is required.")
     *                   ),
     *                   @OA\Property(
     *                       property="groupmenu_id",
     *                       type="array",
     *                       @OA\Items(type="string", example="The groupmenu id field is required.")
     *                   )
     *               )
     *          )
     *       ),
     * )
     */
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

    /**
     * Show option menu by id
     * @OA\Get(
     *     path="/dgush-backend/public/api/optionmenu/{id}",
     *     summary="Get Option Menu by id",
     *     tags={"OptionMenu"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Option Menu id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Option Menu",
     *         @OA\JsonContent(ref="#/components/schemas/OptionMenu")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Option Menu not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Option Menu not found")
     *         )
     *     )
     * )
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
     * Update the Option Menu
     * @OA\Put(
     *     path="/dgush-backend/public/api/optionmenu/{id}",
     *     summary="Update the Option Menu",
     *     tags={"OptionMenu"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the Group Menu",
     *          @OA\Schema(
     *              type="number"
     *          ),
     *          example=1
     *      ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "route", "order", "icon", "groupmenu_id"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="route", type="string"),
     *             @OA\Property(property="order", type="integer"),
     *             @OA\Property(property="icon", type="string"),
     *             @OA\Property(property="groupmenu_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Option Menu updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/OptionMenu")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Group Menu or Option Menu not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Group Menu not found")
     *          )
     *     ),
     *     @OA\Response(
     *           response=422,
     *           description="Validation error",
     *           @OA\JsonContent(
     *               @OA\Property(property="message", type="string", example="The given data was invalid."),
     *               @OA\Property(property="errors", type="object",
     *                   @OA\Property(
     *                       property="name",
     *                       type="array",
     *                       @OA\Items(type="string", example="The name field is required.")
     *                   ),
     *                   @OA\Property(
     *                       property="route",
     *                       type="array",
     *                       @OA\Items(type="string", example="The route field is required.")
     *                   ),
     *                   @OA\Property(
     *                       property="order",
     *                       type="array",
     *                       @OA\Items(type="string", example="The order field is required.")
     *                   ),
     *                   @OA\Property(
     *                       property="icon",
     *                       type="array",
     *                       @OA\Items(type="string", example="The icon field is required.")
     *                   ),
     *                   @OA\Property(
     *                       property="groupmenu_id",
     *                       type="array",
     *                       @OA\Items(type="string", example="The groupmenu id field is required.")
     *                   )
     *               )
     *          )
     *       ),
     * )
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


    /**
     * Delete the Option Menu
     * @OA\Delete(
     *     path="/dgush-backend/public/api/optionmenu/{id}",
     *     summary="Delete the Option Menu",
     *     tags={"OptionMenu"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Option Menu id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Option Menu deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Option Menu deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *    ),
     *     @OA\Response(
     *         response=404,
     *         description="Option Menu not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Option Menu not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Option Menu has accesses associated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Option Menu has accesses associated")
     *         )
     *     )
     * )
     *
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
