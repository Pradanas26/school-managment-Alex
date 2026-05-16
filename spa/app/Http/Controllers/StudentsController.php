<?php

namespace App\Http\Controllers;

/**
 * STUDENTS CONTROLLER — app/Http/Controllers/StudentsController.php
 *
 * Gestió CRUD d'estudiants.
 * Consumeix l'API: GET/POST/PUT/DELETE /api/students
 */
class StudentsController extends Controller
{
    // GET /students
    public function index(): void
    {
        $response = $this->api->getStudents();
        $students = $response['data'] ?? [];
        $error    = $this->apiError($response);

        view('students.index', compact('students', 'error'));
    }

    // GET /students/create
    public function create(): void
    {
        view('students.create', ['errors' => [], 'old' => []]);
    }

    // POST /students
    public function store(): void
    {
        $data = [
            'name'  => trim($_POST['name']  ?? ''),
            'email' => trim($_POST['email'] ?? ''),
        ];

        $errors = $this->validate($data, [
            'name'  => 'required',
            'email' => 'email',
        ]);

        if (!empty($errors)) {
            $this->flashOldInput($data);
            view('students.create', ['errors' => $errors, 'old' => $data]);
            return;
        }

        $response = $this->api->createStudent($data);

        if ($this->apiError($response)) {
            view('students.create', [
                'errors' => ['api' => $this->apiError($response)],
                'old'    => $data,
            ]);
            return;
        }

        flash('success', "Estudiant '{$data['name']}' creat correctament.");
        redirect('/students');
    }

    // GET /students/{id}/edit
    public function edit(string $id): void
    {
        $response = $this->api->getStudent($id);

        if ($this->apiError($response)) {
            view('errors.404');
            return;
        }

        $student = $response['data'];
        view('students.edit', ['student' => $student, 'errors' => []]);
    }

    // PUT /students/{id}
    public function update(string $id): void
    {
        $data = [
            'name'  => trim($_POST['name']  ?? ''),
            'email' => trim($_POST['email'] ?? ''),
        ];

        $errors = $this->validate($data, [
            'name'  => 'required',
            'email' => 'email',
        ]);

        if (!empty($errors)) {
            $student = array_merge(['id' => $id], $data);
            view('students.edit', ['student' => $student, 'errors' => $errors]);
            return;
        }

        $response = $this->api->updateStudent($id, $data);

        if ($this->apiError($response)) {
            $student = array_merge(['id' => $id], $data);
            view('students.edit', [
                'student' => $student,
                'errors'  => ['api' => $this->apiError($response)],
            ]);
            return;
        }

        flash('success', "Estudiant actualitzat correctament.");
        redirect('/students');
    }

    // DELETE /students/{id}
    public function destroy(string $id): void
    {
        $this->api->deleteStudent($id);
        flash('success', "Estudiant eliminat.");
        redirect('/students');
    }
}
