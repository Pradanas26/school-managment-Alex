<?php

namespace App\Http\Controllers;

/**
 * DASHBOARD CONTROLLER
 *
 * Pàgina d'inici amb resum de l'estat del sistema.
 */
class DashboardController extends Controller
{
    public function index(): void
    {
        $studentsResp = $this->api->getStudents();
        $teachersResp = $this->api->getTeachers();
        $subjectsResp = $this->api->getSubjects();

        $students = $studentsResp['data'] ?? [];
        $teachers = $teachersResp['data'] ?? [];
        $subjects = $subjectsResp['data'] ?? [];

        // Estadístiques
        $totalStudents = count($students);
        $totalTeachers = count($teachers);
        $totalSubjects = count($subjects);
        $assignedSubjects = count(array_filter($subjects, fn($s) => !empty($s['teacherId'])));

        // Connexió API
        $apiOnline = ($studentsResp['status'] > 0);

        view('dashboard.index', compact(
            'totalStudents', 'totalTeachers', 'totalSubjects',
            'assignedSubjects', 'apiOnline',
            'students', 'teachers', 'subjects'
        ));
    }
}
