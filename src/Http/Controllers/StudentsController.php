<?php

namespace App\Http\Controllers;

use App\Http\Request;
use App\Http\ResponseJson;
use App\Application\CreateStudent\CreateStudentCommand;
use App\Application\CreateStudent\CreateStudentHandler;
use App\Application\UpdateStudent\UpdateStudentCommand;
use App\Application\UpdateStudent\UpdateStudentHandler;
use App\Domain\Student\StudentId;
use App\Domain\Student\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Infrastructure\Persistence\Doctrine\DoctrineStudentRepository;

class StudentsController
{
    private StudentRepository $studentRepo;
    private CreateStudentHandler $createHandler;
    private UpdateStudentHandler $updateHandler;

    public function __construct(private Request $request, EntityManagerInterface $em)
    {
        $this->studentRepo   = new DoctrineStudentRepository($em);
        $this->createHandler = new CreateStudentHandler($this->studentRepo);
        $this->updateHandler = new UpdateStudentHandler($this->studentRepo);
    }

    // GET /api/students
    public function index(): void
    {
        $students = $this->studentRepo->findAll();

        $data = array_map(fn($s) => [
            'id'    => $s->id()->value(),
            'name'  => $s->name(),
            'email' => $s->email()->value(),
        ], $students);

        (new ResponseJson(200, $data))->send();
    }

    // GET /api/students/{id}
    public function show(string $id): void
    {
        $student = $this->studentRepo->find(new StudentId($id));

        if ($student === null) {
            (new ResponseJson(404, ['error' => 'Student not found']))->send();
            return;
        }

        (new ResponseJson(200, [
            'id'    => $student->id()->value(),
            'name'  => $student->name(),
            'email' => $student->email()->value(),
        ]))->send();
    }

    // POST /api/students
    public function create(): void
    {
        try {
            $body = $this->request->getBody();

            $studentId = StudentId::generate()->value();

            $this->createHandler->handle(new CreateStudentCommand(
                $studentId,
                $body['name']  ?? '',
                $body['email'] ?? ''
            ));

            (new ResponseJson(201, [
                'id'    => $studentId,
                'name'  => $body['name']  ?? '',
                'email' => $body['email'] ?? '',
            ]))->send();

        } catch (\InvalidArgumentException $e) {
            (new ResponseJson(422, ['error' => $e->getMessage()]))->send();
        } catch (\Throwable $e) {
            (new ResponseJson(500, ['error' => 'Internal server error: ' . $e->getMessage()]))->send();
        }
    }

    // PUT /api/students/{id}
    public function update(string $id): void
    {
        try {
            $body = $this->request->getBody();

            $updated = $this->updateHandler->handle(new UpdateStudentCommand(
                $id,
                $body['name']  ?? '',
                $body['email'] ?? ''
            ));

            (new ResponseJson(200, [
                'id'    => $updated->id()->value(),
                'name'  => $updated->name(),
                'email' => $updated->email()->value(),
            ]))->send();

        } catch (\DomainException $e) {
            (new ResponseJson(404, ['error' => $e->getMessage()]))->send();
        } catch (\InvalidArgumentException $e) {
            (new ResponseJson(422, ['error' => $e->getMessage()]))->send();
        } catch (\Throwable $e) {
            (new ResponseJson(500, ['error' => 'Internal server error: ' . $e->getMessage()]))->send();
        }
    }

    // DELETE /api/students/{id}
    public function delete(string $id): void
    {
        try {
            $student = $this->studentRepo->find(new StudentId($id));

            if ($student === null) {
                (new ResponseJson(404, ['error' => 'Student not found']))->send();
                return;
            }

            $this->studentRepo->delete($student);

            (new ResponseJson(200, ['message' => 'Student deleted']))->send();

        } catch (\Throwable $e) {
            (new ResponseJson(500, ['error' => 'Internal server error: ' . $e->getMessage()]))->send();
        }
    }
}
