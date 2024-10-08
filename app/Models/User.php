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
 *    @OA\Property(property="id", type="number", example="11"),
 *    @OA\Property(property="names", type="string", example="D Gush"),
 *    @OA\Property(property="lastnames", type="string", example="Admin"),
 *    @OA\Property(property="email", type="string", example="dgush@gmail.com"),
 *    @OA\Property(property="typeuser_id", type="number", example="2")
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
        'lastnames',
        'email',
        'password',
        'typeuser_id',
        'person_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'accept_terms',
        'login',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
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

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
