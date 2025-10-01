<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\UserModel;

class Admin extends BaseController
{
    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Akses ditolak!');
        }

        $courseModel = new CourseModel();
        $courses = $courseModel->findAll();

        $userModel = new UserModel();
        $students = $userModel->where('role', 'student')->findAll();

        return view('admin/dashboard', [
            'courses' => $courses,
            'students' => $students
        ]);
    }

    public function addCourse()
    {
        $courseModel = new CourseModel();
        $courseModel->insert([
            'course_code' => $this->request->getPost('course_code'),
            'course_name' => $this->request->getPost('course_name'),
            'description' => $this->request->getPost('description'),
        ]);

        return redirect()->to('/admin')->with('success', 'Course ditambahkan!');
    }
}
