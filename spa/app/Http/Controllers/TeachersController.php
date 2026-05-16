<?php

namespace App\Http\Controllers;

/**
 * TEACHERS CONTROLLER — app/Http/Controllers/TeachersController.php
 *
 * Gestió CRUD de professors.
 * Consumeix l'API: GET/POST/PUT/DELETE /api/teachers
 */
class TeachersController extends Controller
{
    // GET /teachers
    public function index(): void
    {
        $response = $this->api->getTeachers();
        $teachers = $response['data'] ?? [];
        $error    = $this->apiError($response);

        view('teachers.index', compact('teachers', 'error'));
    }

    // GET /teachers/create
    public function create(): void
    {
        view('teachers.create', ['errors' => [], 'old' => []]);
    }

    // POST /teachers
    public function store(): void
    {
        $data = [
            'name'      => trim($_POST['name']      ?? ''),
            'specialty' => trim($_POST['specialty'] ?? ''),
        ];

        $errors = $this->validate($data, [
            'name'      => 'required',
            'specialty' => 'required',
        ]);

        if (!empty($errors)) {
            $this->flashOldInput($data);
            view('teachers.create', ['errors' => $errors, 'old' => $data]);
            return;
        }

        $response = $this->api->createTeacher($data);

        if ($this->apiError($response)) {
            view('teachers.create', [
                'errors' => ['api' => $this->apiError($response)],
                'old'    => $data,
            ]);
            return;
        }

        flash('success', "Professor '{$data['name']}' creat correctament.");
        redirect('/teachers');
    }

    // GET /teachers/{id}/edit
    public function edit(string $id): void
    {
        $response = $this->api->getTeacher($id);

        if ($this->apiError($response)) {
            view('errors.404');
            return;
        }

        $teacher = $response['data'];
        view('teachers.edit', ['teacher' => $teacher, 'errors' => []]);
    }

    // PUT /teachers/{id}
    public function update(string $id): void
    {
        $data = [
            'name'      => trim($_POST['name']      ?? ''),
            'specialty' => trim($_POST['specialty'] ?? ''),
        ];

        $errors = $this->validate($data, [
            'name'      => 'required',
            'specialty' => 'required',
        ]);

        if (!empty($errors)) {
            $teacher = array_merge(['id' => $id], $data);
            view('teachers.edit', ['teacher' => $teacher, 'errors' => $errors]);
            return;
        }

        $response = $this->api->updateTeacher($id, $data);

        if ($this->apiError($response)) {
            $teacher = array_merge(['id' => $id], $data);
            view('teachers.edit', [
                'teacher' => $teacher,
                'errors'  => ['api' => $this->apiError($response)],
            ]);
            return;
        }

        flash('success', "Professor actualitzat correctament.");
        redirect('/teachers');
    }

    // DELETE /teachers/{id}
    public function destroy(string $id): void
    {
        $this->api->deleteTeacher($id);
        flash('success', "Professor eliminat.");
        redirect('/teachers');
    }
}
