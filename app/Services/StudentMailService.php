<?php
namespace App\Services;

use App\Mail\StudentWelcomeMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Student;

class StudentMailService
{
    public function sendWelcomeMail(Student $student)
    {
        Mail::to($student->email)->send(new StudentWelcomeMail($student));
    }
}
