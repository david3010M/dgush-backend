<?php

namespace App\Http\Controllers;

use App\Models\GrupoMenu;
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
class GrupoMenuController extends Controller
{
    /**
     * Get all grupo menus
     * @OA\Get (
     *     path="/api/grupomenu",
     *     tags={"Grupo Menus"},
     *     @OA\Response(
     *         response=200,
     *         description="List of active Grupo Menus",
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
        return GrupoMenu::all();
    }

    public function create()
    {
//
    }


    /**
     * Create a new grupo menu
     * @OA\Post (
     *     path="/api/grupomenu",
     *     tags={"Grupo Menus"},
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
     *         description="New Grupo Menu created",
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
    public function store(Request $request)
    {
//        Validar los datos
        $request->validate([
            'name' => 'required',
            'icon' => 'required',
            'order' => 'required',
        ]);

//        Crear un Grupo Menu
        return GrupoMenu::create($request->all());

    }


    /**
     * Show the specified grupo menu
     * @OA\Get (
     *     path="/api/grupomenu/{id}",
     *     tags={"Grupo Menus"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Grupo Menu",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Grupo Menu found",
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
    public function show(int $id)
    {
        return GrupoMenu::find($id);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GrupoMenu $grupoMenu)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GrupoMenu $grupoMenu)
    {
//        Validar los datos
        $request->validate([
            'name' => 'required',
            'icon' => 'required',
            'order' => 'required',
        ]);

//        Actualizar un Grupo Menu
        $grupoMenu->update($request->all());
        return $grupoMenu;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GrupoMenu $grupoMenu)
    {
//        Eliminar un Grupo Menu
        $grupoMenu->delete();
        return response()->json();
    }
}
