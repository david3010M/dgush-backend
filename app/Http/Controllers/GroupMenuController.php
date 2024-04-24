<?php

namespace App\Http\Controllers;

use App\Models\GroupMenu;
use HttpException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *             title="API's D'Gush",
 *             version="1.0",
 *             description="API's for D'Gush store",
 * )
 *
 * @OA\Server(url="http://127.0.0.1:8000")
 */
class GroupMenuController extends Controller
{


    /**
     * Get all Group menus
     * @OA\Get (
     *     path="/api/Groupmenu",
     *     tags={"Group Menus"},
     *     @OA\Response(
     *         response=200,
     *         description="List of active Group Menus",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 type="array",
     *                 property="data",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="number",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Profile"
     *                     ),
     *                     @OA\Property(
     *                         property="order",
     *                         type="number",
     *                         example="10"
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         example="2024-02-23T00:09:16.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         example="2024-02-23T12:13:45.000000Z"
     *                     ),
     *                      @OA\Property(
     *                          property="deleted_at",
     *                          type="string",
     *                          example="2024-02-23T12:30:45.000000Z"
     *                      )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
//        return GroupMenu::paginate(5);
        return GroupMenu::all();
    }

    /**
     * Create a new Group menu
     * @OA\Post (
     *     path="/api/Groupmenu",
     *     tags={"Group Menus"},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "icon", "order"},
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  example="Profile"
     *              ),
     *              @OA\Property(
     *                  property="icon",
     *                  type="string",
     *                  example="fas fa-user"
     *              ),
     *              @OA\Property(
     *                  property="order",
     *                  type="number",
     *                  example="10"
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="New Group Menu created",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="id",
     *                 type="number",
     *                 example="1"
     *             ),
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Profile"
     *             ),
     *             @OA\Property(
     *                 property="order",
     *                 type="number",
     *                 example="10"
     *             ),
     *             @OA\Property(
     *                 property="created_at",
     *                 type="string",
     *                 example="2024-02-23T00:09:16.000000Z"
     *             ),
     *             @OA\Property(
     *                 property="updated_at",
     *                 type="string",
     *                 example="2024-02-23T12:13:45.000000Z"
     *             ),
     *             @OA\Property(
     *                 property="deleted_at",
     *                 type="string",
     *                 example="2024-02-23T12:30:45.000000Z"
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request): GroupMenu|JsonResponse
    {
//        Validate data
        $request->validate([
            'name' => 'required|string|unique:groupmenu',
            'icon' => 'required|string',
            'order' => 'required|integer|unique:groupmenu',
        ]);

//        Create a new Group Menu
        return GroupMenu::create($request->all());
    }

    /**
     * Show the specified Group menu
     * @OA\Get (
     *     path="/api/Groupmenu/{id}",
     *     tags={"Group Menus"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Group Menu",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Group Menu found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="id",
     *                 type="number",
     *                 example="1"
     *             ),
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Profile"
     *             ),
     *             @OA\Property(
     *                 property="order",
     *                 type="number",
     *                 example="10"
     *             ),
     *             @OA\Property(
     *                 property="created_at",
     *                 type="string",
     *                 example="2024-02-23T00:09:16.000000Z"
     *             ),
     *             @OA\Property(
     *                 property="updated_at",
     *                 type="string",
     *                 example="2024-02-23T12:13:45.000000Z"
     *             ),
     *             @OA\Property(
     *                 property="deleted_at",
     *                 type="string",
     *                 example="2024-02-23T12:30:45.000000Z"
     *             )
     *         )
     *     )
     * )
     */
    public function show(int $id): GroupMenu|JsonResponse
    {
//        Find the Group Menu
        $groupMenu = GroupMenu::find($id);

//        Error when not found
        if (!$groupMenu) {
            return response()->json(
                ['message' => 'Group Menu not found'], 404
            );
        }

//        Return the Group Menu
//        return $groupMenu->load('optionMenus');
        return $groupMenu;
    }


    public function update(Request $request, int $id): GroupMenu|JsonResponse
    {
//        Find the Group Menu
        $groupMenu = GroupMenu::find($id);

//        Error when not found
        if (!$groupMenu) {
            return response()->json(
                ['message' => 'Group Menu not found'], 404
            );
        }

//        Validate data
        $request->validate([
            'name' => 'required|string|unique:groupmenu,name,' . $id . ',id',
            'icon' => 'required|string',
            'order' => 'required|integer|unique:groupmenu,order,' . $id . ',id',
        ]);

//        Update the Group Menu
        $groupMenu->update($request->all());
        return $groupMenu;
    }

    public function destroy(int $id): JsonResponse
    {
//        Find the Group Menu
        $groupMenu = GroupMenu::find($id);

//        Error when not found
        if (!$groupMenu) {
            return response()->json(
                ['message' => 'Group Menu not found'], 404
            );
        }

//        Delete the Group Menu
        $groupMenu->delete();
        return response()->json(
            ['message' => 'Option Menu deleted successfully']
        );
    }
}
