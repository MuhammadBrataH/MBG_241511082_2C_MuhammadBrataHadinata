<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;

class Student extends BaseController
{
    public function index()
    {
        if (session()->get('role') !== 'student') {
            return redirect()->to('/login')->with('error', 'Akses ditolak!');
        }

        $courseModel = new CourseModel();
        $courses = $courseModel->findAll();

        return view('student/courses', ['courses' => $courses]);
    }

    public function enroll($courseId)
    {
        if (session()->get('role') !== 'student') {
            return redirect()->to('/login')->with('error', 'Akses ditolak!');
        }

        $enrollModel = new EnrollmentModel();
        $enrollModel->insert([
            'user_id' => session()->get('id'),
            'course_id' => $courseId
        ]);

        return redirect()->to('/student')->with('success', 'Berhasil enroll course!');
    }
}
