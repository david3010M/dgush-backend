<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{

    /**
     * Get all coupons
     * @OA\Get(
     *      path="/dgush-backend/public/api/coupon",
     *      tags={"Coupon"},
     *      summary="Get all coupons",
     *      description="Get all coupons",
     *      security={{"bearerAuth": {}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="current_page",type="integer",),
     *              @OA\Property(property="data",type="array",@OA\Items(ref="#/components/schemas/Coupon")),
     *              @OA\Property(property="first_page_url",type="string",example="http://localhost:8000/api/coupons?page=1"),
     *              @OA\Property(property="from",type="integer",example=1),
     *              @OA\Property(property="next_page_url",type="string", example="http://localhost:8000/api/coupons?page=1"),
     *              @OA\Property(property="path",type="string", example="http://localhost:8000/api/coupons"),
     *              @OA\Property(property="per_page",type="integer",),
     *              @OA\Property(property="prev_page_url",type="string", example="http://localhost:8000/api/coupons?page=1"),
     *              @OA\Property(property="to",type="integer",)
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message",type="string", example="Unauthenticated."),
     *          )
     *      )
     * )
     */
    public function index()
    {
        return response()->json(Coupon::simplePaginate(10));
    }


    /**
     * Create a new coupon
     * @OA\Post(
     *      path="/dgush-backend/public/api/coupon",
     *      tags={"Coupon"},
     *      summary="Create a new coupon",
     *      description="Create a new coupon",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/CouponRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Coupon")
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid data",
     *          @OA\JsonContent(
     *              @OA\Property(property="error",type="string", example="The code has already been taken."),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message",type="string", example="Unauthenticated."),
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'code' => [
                'required',
                'string',
                Rule::unique('coupon', 'code')->whereNull('deleted_at'),
            ],
            'type' => 'required|in:discount,percentage',
            'value' => 'required|numeric',
            'indicator' => 'nullable|in:subtotal,total,sendCost',
            'expires_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'code' => $request->input('code'),
            'type' => $request->input('type'),
            'value' => $request->input('value'),
            'expires_at' => $request->input('expires_at'),
        ];

        $coupon = Coupon::create($data);
        $coupon = Coupon::find($coupon->id);

        return response()->json($coupon);

    }


    /**
     * Get a specific coupon
     * @OA\Get(
     *      path="/dgush-backend/public/api/coupon/{id}",
     *      tags={"Coupon"},
     *      summary="Get a specific coupon",
     *      description="Get a specific coupon",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Coupon ID",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Coupon found",
     *          @OA\JsonContent(ref="#/components/schemas/Coupon")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Coupon not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="error",type="string", example="Coupon not found"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message",type="string", example="Unauthenticated."),
     *          )
     *      )
     * )
     */
    public function show(int $id)
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json(['error' => 'Coupon not found'], 404);
        }

        return response()->json($coupon);
    }

    public function showByCode(string $code)
    {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json(['error' => 'Coupon not found'], 404);
        }

        return response()->json($coupon);
    }

    /**
     * Update a specific coupon
     * @OA\Put(
     *      path="/dgush-backend/public/api/coupon/{id}",
     *      tags={"Coupon"},
     *      summary="Update a specific coupon",
     *      description="Update a specific coupon",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Coupon ID",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/CouponRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Coupon")
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid data",
     *          @OA\JsonContent(
     *              @OA\Property(property="error",type="string", example="The code has already been taken."),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Coupon not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="error",type="string", example="Coupon not found"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message",type="string", example="Unauthenticated."),
     *          )
     *      )
     * )
     */
    public function update(Request $request, int $id)
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json(['error' => 'Coupon not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'code' => [
                'required',
                'string',
                Rule::unique('coupon', 'code')->ignore($coupon->id)->whereNull('deleted_at'),
            ],
            'type' => 'required|in:discount,percentage',
            'indicator' => 'nullable|in:subtotal,total,sendCost',
            'value' => 'required|numeric',
            'expires_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'code' => $request->input('code'),
            'type' => $request->input('type'),
            'indicator' => $request->input('indicator', 'subtotal'),
            'value' => $request->input('value'),
            'expires_at' => $request->input('expires_at'),
        ];

        $coupon->update($data);
        $coupon = Coupon::find($id);

        return response()->json($coupon);
    }


    /**
     * Delete a specific coupon
     * @OA\Delete(
     *      path="/dgush-backend/public/api/coupon/{id}",
     *      tags={"Coupon"},
     *      summary="Delete a specific coupon",
     *      description="Delete a specific coupon",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Coupon ID",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Coupon deleted",
     *          @OA\JsonContent(
     *              @OA\Property(property="message",type="string", example="Coupon deleted"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Coupon not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="error",type="string", example="Coupon not found"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message",type="string", example="Unauthenticated."),
     *          )
     *      )
     * )
     */
    public function destroy(int $id)
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json(['error' => 'Coupon not found'], 404);
        }

        $coupon->delete();

        return response()->json(['message' => 'Coupon deleted']);
    }
}
