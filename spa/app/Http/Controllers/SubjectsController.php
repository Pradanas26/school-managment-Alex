<?php

namespace App\Http\Controllers;

/**
 * SUBJECTS CONTROLLER — app/Http/Controllers/SubjectsController.php
 *
 * Gestió CRUD d'assignatures + assignació de professor.
 * Consumeix l'API: GET/POST/PUT/DELETE /api/subjects
 *                  POST /api/subjects/{id}/assign-teacher
 */
class SubjectsController extends Controller
{
    // GET /subjects
    public function index(): void
    {
        $response  = $this->api->getSubjects();
        $subjects  = $response['data'] ?? [];
        $error     = $this->apiError($response);

        // Carreguem els professors per mostrar el nom en lloc de l'ID
        $teachersResp = $this->api->getTeachers();
        $teachersMap  = [];
        foreach ($teachersResp['data'] ?? [] as $t) {
            $teachersMap[$t['id']] = $t['name'];
        }

        view('subjects.index', compact('subjects', 'teachersMap', 'error'));
    }

    // GET /subjects/create
    public function create(): void
    {
        $teachersResp = $this->api->getTeachers();
        $teachers     = $teachersResp['data'] ?? [];

        $coursesResp  = $this->api->getCourses();
        $courses      = $coursesResp['data'] ?? [];

        view('subjects.create', ['teachers' => $teachers, 'courses' => $courses, 'errors' => [], 'old' => []]);
    }

    // POST /subjects
    public function store(): void
    {
        $data = [
            'name'     => trim($_POST['name']     ?? ''),
            'courseId' => trim($_POST['courseId'] ?? ''),
        ];

        $errors = $this->validate($data, [
            'name'     => 'required',
            'courseId' => 'required',
        ]);

        if (!empty($errors)) {
            $this->flashOldInput($data);
            $teachersResp = $this->api->getTeachers();
            $teachers     = $teachersResp['data'] ?? [];
            $coursesResp  = $this->api->getCourses();
            $courses      = $coursesResp['data'] ?? [];
            view('subjects.create', ['teachers' => $teachers, 'courses' => $courses, 'errors' => $errors, 'old' => $data]);
            return;
        }

        $response = $this->api->createSubject($data);

        if ($this->apiError($response)) {
            $teachersResp = $this->api->getTeachers();
            $teachers     = $teachersResp['data'] ?? [];
            $coursesResp  = $this->api->getCourses();
            $courses      = $coursesResp['data'] ?? [];
            view('subjects.create', [
                'teachers' => $teachers,
                'courses'  => $courses,
                'errors'   => ['api' => $this->apiError($response)],
                'old'      => $data,
            ]);
            return;
        }

        flash('success', "Assignatura '{$data['name']}' creada correctament.");
        redirect('/subjects');
    }

    // GET /subjects/{id}/edit
    public function edit(string $id): void
    {
        $response = $this->api->getSubject($id);

        if ($this->apiError($response)) {
            view('errors.404');
            return;
        }

        $subject      = $response['data'];
        $teachersResp = $this->api->getTeachers();
        $teachers     = $teachersResp['data'] ?? [];
        $coursesResp  = $this->api->getCourses();
        $coursesMap   = [];
        foreach ($coursesResp['data'] ?? [] as $c) {
            $coursesMap[$c['id']] = $c['name'];
        }

        view('subjects.edit', ['subject' => $subject, 'teachers' => $teachers, 'coursesMap' => $coursesMap, 'errors' => []]);
    }

    // PUT /subjects/{id}
    public function update(string $id): void
    {
        $data = [
            'name' => trim($_POST['name'] ?? ''),
        ];

        $errors = $this->validate($data, ['name' => 'required']);

        if (!empty($errors)) {
            $subject      = array_merge(['id' => $id], $data);
            $teachersResp = $this->api->getTeachers();
            $teachers     = $teachersResp['data'] ?? [];
            view('subjects.edit', ['subject' => $subject, 'teachers' => $teachers, 'errors' => $errors]);
            return;
        }

        $response = $this->api->updateSubject($id, $data);

        if ($this->apiError($response)) {
            $subject      = array_merge(['id' => $id], $data);
            $teachersResp = $this->api->getTeachers();
            $teachers     = $teachersResp['data'] ?? [];
            view('subjects.edit', [
                'subject'  => $subject,
                'teachers' => $teachers,
                'errors'   => ['api' => $this->apiError($response)],
            ]);
            return;
        }

        flash('success', "Assignatura actualitzada correctament.");
        redirect('/subjects');
    }

    // POST /subjects/{id}/assign-teacher
    public function assignTeacher(string $id): void
    {
        $teacherId = trim($_POST['teacherId'] ?? '');

        if (empty($teacherId)) {
            flash('error', "Has de seleccionar un professor.");
            redirect('/subjects');
            return;
        }

        $response = $this->api->assignTeacherToSubject($id, $teacherId);

        if ($this->apiError($response)) {
            flash('error', $this->apiError($response));
        } else {
            flash('success', "Professor assignat correctament.");
        }

        redirect('/subjects');
    }

    // DELETE /subjects/{id}
    public function destroy(string $id): void
    {
        $this->api->deleteSubject($id);
        flash('success', "Assignatura eliminada.");
        redirect('/subjects');
    }
}