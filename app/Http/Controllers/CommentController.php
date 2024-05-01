<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    /**
     * @OA\Get(
     *     path="/dgush-backend/public/api/comment",
     *     tags={"Comment"},
     *     summary="Get all comments",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of comments",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Comment")
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
     *
     */
    public function index()
    {
        return Comment::all();
    }


    /**
     * CREATE COMMENT
     * @OA\Post(
     *     path="/dgush-backend/public/api/comment",
     *     tags={"Comment"},
     *     summary="Create a comment",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"description", "score", "user_id", "product_id"},
     *             @OA\Property(property="description", type="string", example="This is a comment"),
     *             @OA\Property(property="score", type="integer", example="5"),
     *             @OA\Property(property="user_id", type="integer", example="1"),
     *             @OA\Property(property="product_id", type="integer", example="1"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment created",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User or Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="User already commented this product",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User already commented this product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid.")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
//        VALIDATE DATA
        $request->validate([
            'description' => 'required|string',
            'score' => 'required|integer',
            'user_id' => 'required|integer',
            'product_id' => 'required|integer',
        ]);

//        VALIDATE USER AND PRODUCT
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

//        VALIDATE IF USER AND PRODUCT NOT EXISTS BOTH
        $comment = Comment::where('user_id', $request->user_id)
            ->where('product_id', $request->product_id)
            ->first();
        if ($comment) {
            return response()->json(['message' => 'User already commented this product'], 400);
        }

//        CREATE COMMENT
        $comment = Comment::create($request->all());
        return response()->json($comment);
    }


    /**
     * SHOW COMMENT
     * @OA\Get(
     *     path="/dgush-backend/public/api/comment/{id}",
     *     tags={"Comment"},
     *     summary="Get a comment",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Comment ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment found",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Comment not found")
     *         )
     *     )
     * )
     */
    public function show(int $id)
    {
        $comment = Comment::find($id);
        if ($comment) {
            return response()->json($comment);
        } else {
            return response()->json(['message' => 'Comment not found'], 404);
        }
    }


    /**
     * UPDATE COMMENT
     * @OA\Put(
     *     path="/dgush-backend/public/api/comment/{id}",
     *     tags={"Comment"},
     *     summary="Update a comment",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Comment ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"description", "score", "user_id", "product_id"},
     *             @OA\Property(property="description", type="string", example="This is a comment"),
     *             @OA\Property(property="score", type="integer", example="5"),
     *             @OA\Property(property="user_id", type="integer", example="1"),
     *             @OA\Property(property="product_id", type="integer", example="1"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment updated",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comment, User or Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Comment not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="User already commented this product",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User already commented this product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}})
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id)
    {
        $comment = Comment::find($id);
        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

//        VALIDATE DATA
        $request->validate([
            'description' => 'required|string',
            'score' => 'required|integer',
            'user_id' => 'required|integer',
            'product_id' => 'required|integer',
        ]);

//        VALIDATE USER AND PRODUCT
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

//        VALIDATE IF USER AND PRODUCT NOT EXISTS BOTH AND NOT SAME
        $comment = Comment::where('user_id', $request->user_id)
            ->where('product_id', $request->product_id)
            ->first();
        if ($comment && $comment->id != $id) {
            return response()->json(['message' => 'User already commented this product'], 409);
        }

//        UPDATE COMMENT
        $comment->update([
            'description' => $request->description,
            'score' => $request->score,
            'user_id' => $request->user_id,
            'product_id' => $request->product_id,
        ]);

        return response()->json($comment);

    }


    /**
     * DELETE COMMENT
     * @OA\Delete(
     *     path="/dgush-backend/public/api/comment/{id}",
     *     tags={"Comment"},
     *     summary="Delete a comment",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Comment ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Comment deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Comment not found")
     *         )
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $comment = Comment::find($id);
        if ($comment) {
            $comment->delete();
            return response()->json(['message' => 'Comment deleted']);
        } else {
            return response()->json(['message' => 'Comment not found'], 404);
        }
    }
}
