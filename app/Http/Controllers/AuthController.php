<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Http\Requests\registerTeacherRequest;
use App\Http\Requests\loginTeacherRequest;
use App\Http\Requests\updateTeacherRequest;

class AuthController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/teachers",
     *     summary="Get all teachers",
     *     tags={"Teachers"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Here is the list of all teachers:"),
     *             @OA\Property(property="teacher", type="array", @OA\Items(ref="#/components/schemas/User"))
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */

    public function index(Request $request)
    {
        try {
            $teacher = User::all();
            return response()->json([
                'message' => 'Here is the list of all teachers:',
                'teacher' => $teacher,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Unknown Eror Occured.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new teacher",
     *     tags={"Teachers"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "role"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="role", type="string", example="teacher")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Teacher registered successfully"
     *     ),
     *     @OA\Response(response=500, description="Registration failed") 
     * )
     */

    public function register(registerTeacherRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            return response()->json([
                'message' => 'User registered with the defined role',
                'user' => $user
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'User registration failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login as teacher",
     *     tags={"Teachers"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Login successful"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */


    public function login(LoginTeacherRequest $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('API Token')->accessToken;

            return response()->json([
                'message' => 'Teacher Login Successfully',
                'token' => $token
            ], 200);
        }

        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }


    /**
     * @OA\Get(
     *     path="/api/teachers/{id}",
     *     summary="Get a specific teacher by ID",
     *     tags={"Teachers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Teacher ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Teacher details"),
     *     @OA\Response(response=404, description="Teacher not found"),
     *     security={{"bearerAuth":{}}}
     * )
     */


    public function show($id)
    {
        try {
            $teacher = User::find($id);
            return response()->json([
                'message' => 'here is the teacher you have searched of:',
                'teacher' => $teacher,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Unknown Error Occured',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * @OA\Put(
     *     path="/api/teachers/{id}",
     *     summary="Update a teacher's data",
     *     tags={"Teachers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Teacher ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Teacher updated"),
     *     @OA\Response(response=404, description="Not found"),
     *     security={{"bearerAuth":{}}}
     * )
     */

    public function update(updateTeacherRequest $request, $id)
    {
        try {
            $teacher = User::findOrFail($id);
            if (!$teacher)
                return response()->json(['message' => 'Teacher Not found'], 404);

            $teacher->update($request->all());

            return response()->json([
                'message' => 'Teacher updated',
                'teacher' => $teacher
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to update teacher',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // public function logout(Request $request)
    // {
    //     try {
    //         return response()->json([
    //             'messgae' => 'User logged out successfully.',
    //         ], 201);
    //         $request->user()->token()->revoke();
    //     } catch (Exception $e) {
    //         return response()->json([
    //             'message' => 'User Logout Failed, try again.',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }


    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout teacher",
     *     tags={"Teachers"},
     *     @OA\Response(response=201, description="Logout successful"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"bearerAuth":{}}}
     * )
     */

    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'message' => 'Unauthorized',
                ], 401);
            }

            $request->user()->token()->revoke();
            return response()->json([
                'message' => 'The teacher has been logged out successfully.',
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Unknown Error Occured.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/teachers/{id}",
     *     summary="Delete a teacher",
     *     tags={"Teachers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Teacher ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=201, description="Teacher deleted"),
     *     @OA\Response(response=404, description="Teacher not found"),
     *     security={{"bearerAuth":{}}}
     * )
     */


    public function delete($id)
    {
        try {
            $teacher = User::findOrFail($id);
            if (!$teacher)
                return response()->json(['message' => 'Not found'], 404);
            $teacher->delete();
            return response()->json([
                'message' => 'Student Deleted Successsfully.'
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Teacher can not be deleted.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}


