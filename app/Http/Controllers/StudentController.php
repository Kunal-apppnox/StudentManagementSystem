<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Student;
use Illuminate\Support\Facades\Mail;
use App\Mail\StudentWelcomeMail;
use App\Services\StudentMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentrequest;
use OpenApi\Annotations as OA;


/**
 * @OA\Info(
 *     title="Student Management API",
 *     version="1.0.0",
 *     description="API for managing student records",
 *     @OA\Contact(
 *         email="support@example.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local development server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * @OA\Tag(
 *     name="Students",
 *     description="API Endpoints for Student Management"
 * )
 * 
 * @OA\Schema(
 *     schema="Student",
 *     type="object",
 *     required={"name", "email", "age"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="age", type="integer", example=20),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="StoreStudentRequest",
 *     type="object",
 *     required={"name", "email", "age"},
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="age", type="integer", example=20)
 * )
 *
 * @OA\Schema(
 *     schema="UpdateStudentRequest",
 *     type="object",
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="age", type="integer", example=20)
 * )
 */


class StudentController extends Controller
{
    private $studentMailService;
    public function __construct(StudentMailService $studentMailService)
    {
        $this->studentMailService = $studentMailService;
    }

    /**
     * @OA\Get(
     *     path="/api/students",
     *     summary="Get all students",
     *     tags={"Students"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Here is the list of all students:"
     *             ),
     *             @OA\Property(
     *                 property="students",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Student")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unknown Error Occured"),
     *             @OA\Property(property="error", type="string", example="Error Message details")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $students = Student::all();
            return response()->json([
                'message' => 'Listing of all students:',
                'students' => $students,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Unknown Eror Occured.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /**
     * @OA\Get(
     *     path="/api/students/{id}",
     *     summary="Get a specific student",
     *     tags={"Students"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Student ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="here is the student you have searched of:"),
     *             @OA\Property(property="student", ref="#/components/schemas/Student")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unknown Error Occurred"),
     *             @OA\Property(property="error", type="string", example="Error message details")
     *         )
     *     )
     * )
     */

    public function show($id)
    {
        try {
            $student = Student::find($id);
            return response()->json([
                'message' => 'here is the student you have searched of:',
                'student' => $student,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Unknown Error Occured',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/students",
     *     summary="Create a new student",
     *     tags={"Students"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreStudentRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Student created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="New Student has been created and Mail has been sent."),
     *             @OA\Property(property="student", ref="#/components/schemas/Student")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="New Student creation failed."),
     *             @OA\Property(property="error", type="string", example="Error message details")
     *         )
     *     )
     * )
     */
    public function store(storeStudentRequest $request)
    {
        try {
            $student = Student::create([
                'name' => $request->name,
                'age' => $request->age,
                'email' => $request->email,
            ]);
            //    Mail::to($student->email)->send(new StudentWelcomeMail($student)); 
            $this->studentMailService->sendWelcomeMail($student);
            return response()->json([
                'success' => true,
                'message' => 'New Student has been created and Mail has been sent.',
                'student' => $student,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'New Student creation failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/students/{id}",
     *     summary="Update a student",
     *     tags={"Students"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Student ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateStudentRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User updated"),
     *             @OA\Property(property="student", ref="#/components/schemas/Student")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to update student"),
     *             @OA\Property(property="error", type="string", example="Error message details")
     *         )
     *     )
     * )
     */
    public function update(updateStudentrequest $request, $id)
    {
        try {
            $student = Student::findOrFail($id);
            if (!$student)
                return response()->json(['message' => 'Not found'], 404);

            $student->update($request->all());

            return response()->json([
                'message' => 'User updated',
                'student' => $student
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to update student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/students/{id}",
     *     summary="Delete a student",
     *     tags={"Students"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Student ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Student Deleted Successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Student can not be deleted."),
     *             @OA\Property(property="error", type="string", example="Error message details")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $student = Student::findOrFail($id);
            if (!$student)
                return response()->json(['message' => 'Not found'], 404);
            $student->delete();
            return response()->json([
                'message' => 'Student Deleted Successfully.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Student can not be deleted.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

