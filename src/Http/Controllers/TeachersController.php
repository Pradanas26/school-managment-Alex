<?php

namespace App\Http\Controllers;

use App\Http\Request;
use App\Http\ResponseJson;
use App\Application\CreateTeacher\CreateTeacherCommand;
use App\Application\CreateTeacher\CreateTeacherHandler;
use App\Application\UpdateTeacher\UpdateTeacherCommand;
use App\Application\UpdateTeacher\UpdateTeacherHandler;
use App\Domain\Teacher\TeacherId;
use App\Domain\Teacher\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Infrastructure\Persistence\Doctrine\DoctrineTeacherRepository;

class TeachersController
{
    private TeacherRepository $teacherRepo;
    private CreateTeacherHandler $createHandler;
    private UpdateTeacherHandler $updateHandler;

    public function __construct(private Request $request, EntityManagerInterface $em)
    {
        $this->teacherRepo   = new DoctrineTeacherRepository($em);
        $this->createHandler = new CreateTeacherHandler($this->teacherRepo);
        $this->updateHandler = new UpdateTeacherHandler($this->teacherRepo);
    }

    // GET /api/teachers
    public function index(): void
    {
        $teachers = $this->teacherRepo->findAll();

        $data = array_map(fn($t) => [
            'id'        => $t->id()->value(),
            'name'      => $t->name(),
            'specialty' => $t->specialty(),
        ], $teachers);

        (new ResponseJson(200, $data))->send();
    }

    // GET /api/teachers/{id}
    public function show(string $id): void
    {
        $teacher = $this->teacherRepo->find(new TeacherId($id));

        if ($teacher === null) {
            (new ResponseJson(404, ['error' => 'Teacher not found']))->send();
            return;
        }

        (new ResponseJson(200, [
            'id'        => $teacher->id()->value(),
            'name'      => $teacher->name(),
            'specialty' => $teacher->specialty(),
        ]))->send();
    }

    // POST /api/teachers
    public function create(): void
    {
        try {
            $body = $this->request->getBody();

            $teacherId = TeacherId::generate()->value();

            $this->createHandler->handle(new CreateTeacherCommand(
                $teacherId,
                $body['name']      ?? '',
                $body['specialty'] ?? ''
            ));

            (new ResponseJson(201, [
                'id'        => $teacherId,
                'name'      => $body['name']      ?? '',
                'specialty' => $body['specialty'] ?? '',
            ]))->send();

        } catch (\InvalidArgumentException $e) {
            (new ResponseJson(422, ['error' => $e->getMessage()]))->send();
        } catch (\Throwable $e) {
            (new ResponseJson(500, ['error' => 'Internal server error: ' . $e->getMessage()]))->send();
        }
    }

    // PUT /api/teachers/{id}
    public function update(string $id): void
    {
        try {
            $body = $this->request->getBody();

            $updated = $this->updateHandler->handle(new UpdateTeacherCommand(
                $id,
                $body['name']      ?? '',
                $body['specialty'] ?? ''
            ));

            (new ResponseJson(200, [
                'id'        => $updated->id()->value(),
                'name'      => $updated->name(),
                'specialty' => $updated->specialty(),
            ]))->send();

        } catch (\DomainException $e) {
            (new ResponseJson(404, ['error' => $e->getMessage()]))->send();
        } catch (\InvalidArgumentException $e) {
            (new ResponseJson(422, ['error' => $e->getMessage()]))->send();
        } catch (\Throwable $e) {
            (new ResponseJson(500, ['error' => 'Internal server error: ' . $e->getMessage()]))->send();
        }
    }

    // DELETE /api/teachers/{id}
    public function delete(string $id): void
    {
        try {
            $teacher = $this->teacherRepo->find(new TeacherId($id));

            if ($teacher === null) {
                (new ResponseJson(404, ['error' => 'Teacher not found']))->send();
                return;
            }

            $this->teacherRepo->delete($teacher);

            (new ResponseJson(200, ['message' => 'Teacher deleted']))->send();

        } catch (\Throwable $e) {
            (new ResponseJson(500, ['error' => 'Internal server error: ' . $e->getMessage()]))->send();
        }
    }
}
