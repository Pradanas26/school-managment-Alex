<?php

namespace App\Http\Controllers;

use App\Http\Request;
use App\Http\ResponseJson;
use App\Application\CreateSubject\CreateSubjectCommand;
use App\Application\CreateSubject\CreateSubjectHandler;
use App\Application\UpdateSubject\UpdateSubjectCommand;
use App\Application\UpdateSubject\UpdateSubjectHandler;
use App\Application\AssignTeacherToSubject\AssignTeacherToSubjectCommand;
use App\Application\AssignTeacherToSubject\AssignTeacherToSubjectHandler;
use App\Domain\Subject\SubjectId;
use App\Domain\Subject\SubjectRepository;
use App\Domain\Teacher\TeacherRepository;
use App\Domain\Assignment\AssignmentRepository;
use App\Domain\Course\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Infrastructure\Persistence\Doctrine\DoctrineSubjectRepository;
use App\Infrastructure\Persistence\Doctrine\DoctrineTeacherRepository;
use App\Infrastructure\Persistence\Doctrine\DoctrineAssignmentRepository;
use App\Infrastructure\Persistence\Doctrine\DoctrineCourseRepository;

class SubjectsController
{
    private SubjectRepository    $subjectRepo;
    private TeacherRepository    $teacherRepo;
    private AssignmentRepository $assignmentRepo;
    private CourseRepository     $courseRepo;
    private CreateSubjectHandler $createHandler;
    private UpdateSubjectHandler $updateHandler;
    private AssignTeacherToSubjectHandler $assignHandler;

    public function __construct(private Request $request, EntityManagerInterface $em)
    {
        $this->subjectRepo    = new DoctrineSubjectRepository($em);
        $this->teacherRepo    = new DoctrineTeacherRepository($em);
        $this->assignmentRepo = new DoctrineAssignmentRepository($em);
        $this->courseRepo     = new DoctrineCourseRepository($em);

        $this->createHandler = new CreateSubjectHandler($this->subjectRepo, $this->courseRepo);
        $this->updateHandler = new UpdateSubjectHandler($this->subjectRepo);
        $this->assignHandler = new AssignTeacherToSubjectHandler(
            $this->teacherRepo,
            $this->subjectRepo,
            $this->assignmentRepo
        );
    }

    // GET /api/subjects
    public function index(): void
    {
        $subjects = $this->subjectRepo->findAll();

        $data = array_map(fn($s) => [
            'id'        => $s->id()->value(),
            'name'      => $s->name(),
            'courseId'  => $s->courseId()->value(),
            'teacherId' => $s->teacherId()?->value(),
        ], $subjects);

        (new ResponseJson(200, $data))->send();
    }

    // GET /api/subjects/{id}
    public function show(string $id): void
    {
        $subject = $this->subjectRepo->find(new SubjectId($id));

        if ($subject === null) {
            (new ResponseJson(404, ['error' => 'Subject not found']))->send();
            return;
        }

        (new ResponseJson(200, [
            'id'        => $subject->id()->value(),
            'name'      => $subject->name(),
            'courseId'  => $subject->courseId()->value(),
            'teacherId' => $subject->teacherId()?->value(),
        ]))->send();
    }

    // POST /api/subjects
    public function create(): void
    {
        try {
            $body = $this->request->getBody();

            $subjectId = SubjectId::generate()->value();

            $this->createHandler->handle(new CreateSubjectCommand(
                $subjectId,
                $body['name']     ?? '',
                $body['courseId'] ?? ''
            ));

            (new ResponseJson(201, [
                'id'       => $subjectId,
                'name'     => $body['name']     ?? '',
                'courseId' => $body['courseId'] ?? '',
            ]))->send();

        } catch (\InvalidArgumentException $e) {
            (new ResponseJson(422, ['error' => $e->getMessage()]))->send();
        } catch (\Throwable $e) {
            (new ResponseJson(500, ['error' => 'Internal server error: ' . $e->getMessage()]))->send();
        }
    }

    // PUT /api/subjects/{id}
    public function update(string $id): void
    {
        try {
            $body = $this->request->getBody();

            $updated = $this->updateHandler->handle(new UpdateSubjectCommand(
                $id,
                $body['name'] ?? ''
            ));

            (new ResponseJson(200, [
                'id'        => $updated->id()->value(),
                'name'      => $updated->name(),
                'courseId'  => $updated->courseId()->value(),
                'teacherId' => $updated->teacherId()?->value(),
            ]))->send();

        } catch (\DomainException $e) {
            (new ResponseJson(404, ['error' => $e->getMessage()]))->send();
        } catch (\InvalidArgumentException $e) {
            (new ResponseJson(422, ['error' => $e->getMessage()]))->send();
        } catch (\Throwable $e) {
            (new ResponseJson(500, ['error' => 'Internal server error: ' . $e->getMessage()]))->send();
        }
    }

    // POST /api/subjects/{id}/assign-teacher
    public function assignTeacher(string $id): void
    {
        try {
            $body = $this->request->getBody();

            $this->assignHandler->handle(new AssignTeacherToSubjectCommand(
                $body['teacherId'] ?? '',
                $id
            ));

            $subject = $this->subjectRepo->find(new SubjectId($id));

            (new ResponseJson(200, [
                'id'        => $subject->id()->value(),
                'name'      => $subject->name(),
                'courseId'  => $subject->courseId()->value(),
                'teacherId' => $subject->teacherId()?->value(),
            ]))->send();

        } catch (\InvalidArgumentException $e) {
            (new ResponseJson(422, ['error' => $e->getMessage()]))->send();
        } catch (\Throwable $e) {
            (new ResponseJson(500, ['error' => 'Internal server error: ' . $e->getMessage()]))->send();
        }
    }

    // DELETE /api/subjects/{id}
    public function delete(string $id): void
    {
        try {
            $subject = $this->subjectRepo->find(new SubjectId($id));

            if ($subject === null) {
                (new ResponseJson(404, ['error' => 'Subject not found']))->send();
                return;
            }

            $this->subjectRepo->delete($subject);

            (new ResponseJson(200, ['message' => 'Subject deleted']))->send();

        } catch (\Throwable $e) {
            (new ResponseJson(500, ['error' => 'Internal server error: ' . $e->getMessage()]))->send();
        }
    }
}
