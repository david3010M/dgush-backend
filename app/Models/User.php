<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


/**
 * @OA\Schema(
 *    schema="User",
 *    type="object",
 *    @OA\Property(
 *      property="id",
 *      type="number",
 *      example="11"
 *    ),
 *    @OA\Property(
 *      property="names",
 *      type="string",
 *      example="D Gush"
 *    ),
 *    @OA\Property(
 *      property="email",
 *      type="string",
 *      example="dgush@gmail.com"
 *    ),
 *    @OA\Property(
 *      property="typeuser_id",
 *      type="number",
 *      example="2"
 *    ),
 *    @OA\Property(
 *      property="created_at",
 *      type="string",
 *      example="2024-02-23T00:09:16.000000Z"
 *    ),
 *    @OA\Property(
 *      property="updated_at",
 *      type="string",
 *      example="2024-02-23T12:13:45.000000Z"
 *    ),
 *    @OA\Property(
 *      property="deleted_at",
 *      type="string",
 *      example="null",
 *    )
 * )
 */
class User extends Authenticatable
{


    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'names',
        'email',
        'password',
        'typeuser_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'login',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function typeuser()
    {
        return $this->belongsTo(TypeUser::class);
    }
}
