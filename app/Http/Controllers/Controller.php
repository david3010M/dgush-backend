<?php

namespace App\Http\Controllers;

use App\Traits\Filterable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 *
 * @OA\Schema (
 *      schema="PaginationLinks",
 *      @OA\Property(property="first", type="string", example="https://develop.garzasoft.com/dgush-backend/public/api/path?page=1"),
 *      @OA\Property(property="last", type="string", example="https://develop.garzasoft.com/dgush-backend/public/api/path?page=4"),
 *      @OA\Property(property="prev", type="string", example="null"),
 *      @OA\Property(property="next", type="string", example="https://develop.garzasoft.com/dgush-backend/public/api/path?page=2")
 *  )
 *
 * @OA\Schema (
 *      schema="PaginationMetaLinks",
 *      @OA\Property(property="url", type="string", example="https://develop.garzasoft.com/dgush-backend/public/api/path?page=1"),
 *      @OA\Property(property="label", type="string", example="1"),
 *      @OA\Property(property="active", type="boolean", example="true")
 *  )
 *
 * @OA\Schema (
 *      schema="PaginationMeta",
 *      @OA\Property(property="current_page", type="integer", example="1"),
 *      @OA\Property(property="from", type="integer", example="1"),
 *      @OA\Property(property="last_page", type="integer", example="4"),
 *      @OA\Property(property="links", type="object", ref="#/components/schemas/PaginationMetaLinks"),
 *      @OA\Property(property="path", type="string", example="https://develop.garzasoft.com/dgush-backend/public/api/path"),
 *      @OA\Property(property="per_page", type="integer", example="15"),
 *      @OA\Property(property="to", type="integer", example="15"),
 *      @OA\Property(property="total", type="integer", example="60")
 *  )
 *
 * @OA\Schema (
 *      schema="ValidationError",
 *      @OA\Property(property="error", type="string", example="The pagination must be an integer.")
 *  )
 *
 * @OA\Schema (
 *      schema="Unauthenticated",
 *      @OA\Property(property="error", type="string", example="Unauthenticated.")
 *  )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, Filterable;
}
