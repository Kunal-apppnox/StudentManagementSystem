<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    /**
     * @OA\Schema(
     *     schema="User",
     *     type="object",
     *     title="User",
     *     description="User model",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="Kunal Sharma"),
     *     @OA\Property(property="email", type="string", example="kunal@appnox.com"),
     *     @OA\Property(property="role", type="string", example="teacher"),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-06-01T12:00:00Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-06-01T12:00:00Z")
     * )
     */

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
