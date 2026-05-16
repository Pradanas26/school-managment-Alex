<?php

namespace App\Services;

/**
 * DIRECT API CLIENT — spa/app/Services/ApiClient.php
 *
 * En lloc de fer peticions HTTP (que no funcionen amb el servidor
 * built-in de PHP, que és single-threaded), aquest client crida
 * directament els handlers de l'aplicació i els repositoris Doctrine.
 *
 * Mateixa interfície pública que l'anterior ApiClient basat en cURL:
 * els Controllers del SPA no noten cap diferència.
 */

// ── Doctrine & Infrastructure ────────────────────────────────────────────────
use App\Infrastructure\Persistence\Doctrine\DoctrineStudentRepository;
use App\Infrastructure\Persistence\Doctrine\DoctrineTeacherRepository;
use App\Infrastructure\Persistence\Doctrine\DoctrineSubjectRepository;
use App\Infrastructure\Persistence\Doctrine\DoctrineAssignmentRepository;

// ── Application Handlers ─────────────────────────────────────────────────────
use App\Application\CreateStudent\CreateStudentHandler;
use App\Application\CreateStudent\CreateStudentCommand;
use App\Application\UpdateStudent\UpdateStudentHandler;
use App\Application\UpdateStudent\UpdateStudentCommand;

use App\Application\CreateTeacher\CreateTeacherHandler;
use App\Application\CreateTeacher\CreateTeacherCommand;
use App\Application\UpdateTeacher\UpdateTeacherHandler;
use App\Application\UpdateTeacher\UpdateTeacherCommand;

use App\Application\CreateSubject\CreateSubjectHandler;
use App\Application\CreateSubject\CreateSubjectCommand;
use App\Application\UpdateSubject\UpdateSubjectHandler;
use App\Application\UpdateSubject\UpdateSubjectCommand;

use App\Application\AssignTeacherToSubject\AssignTeacherToSubjectHandler;
use App\Application\AssignTeacherToSubject\AssignTeacherToSubjectCommand;

// ── Domain ───────────────────────────────────────────────────────────────────
use App\Domain\Student\StudentId;
use App\Domain\Teacher\TeacherId;
use App\Domain\Subject\SubjectId;

class ApiClient
{
    private \Doctrine\ORM\EntityManagerInterface $em;
    private DoctrineStudentRepository  $students;
    private DoctrineTeacherRepository  $teachers;
    private DoctrineSubjectRepository  $subjects;
    private DoctrineAssignmentRepository $assignments;

    public function __construct()
    {
        $this->em = require BASE_PATH . '/config/doctrine.php';

        // Crea les taules si no existeixen (igual que fa api.php i index.php)
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $schemaTool->updateSchema([
            $this->em->getClassMetadata(\App\Domain\Student\Student::class),
            $this->em->getClassMetadata(\App\Domain\Course\Course::class),
            $this->em->getClassMetadata(\App\Domain\Subject\Subject::class),
            $this->em->getClassMetadata(\App\Domain\Teacher\Teacher::class),
            $this->em->getClassMetadata(\App\Domain\Enrollment\Enrollment::class),
            $this->em->getClassMetadata(\App\Domain\Assignment\Assignment::class),
        ], true);

        $this->students    = new DoctrineStudentRepository($this->em);
        $this->teachers    = new DoctrineTeacherRepository($this->em);
        $this->subjects    = new DoctrineSubjectRepository($this->em);
        $this->assignments = new DoctrineAssignmentRepository($this->em);
    }

    // ── Students ─────────────────────────────────────────────────────────────

    public function getStudents(): array
    {
        try {
            $all = $this->students->findAll();
            return ['status' => 200, 'data' => array_map([$this, 'serializeStudent'], $all), 'error' => null];
        } catch (\Throwable $e) {
            return $this->err($e);
        }
    }

    public function getStudent(string $id): array
    {
        try {
            $s = $this->students->find(new StudentId($id));
            if (!$s) return ['status' => 404, 'data' => [], 'error' => 'Estudiant no trobat'];
            return ['status' => 200, 'data' => $this->serializeStudent($s), 'error' => null];
        } catch (\Throwable $e) {
            return $this->err($e);
        }
    }

    public function createStudent(array $data): array
    {
        try {
            $id = 'student_' . uniqid('', true);
            $handler = new CreateStudentHandler($this->students);
            $handler->handle(new CreateStudentCommand($id, $data['name'], $data['email'] ?? ''));
            return ['status' => 201, 'data' => [], 'error' => null];
        } catch (\Throwable $e) {
            return $this->err($e);
        }
    }

    public function updateStudent(string $id, array $data): array
    {
        try {
            $handler = new UpdateStudentHandler($this->students);
            $handler->handle(new UpdateStudentCommand($id, $data['name'], $data['email'] ?? ''));
            return ['status' => 200, 'data' => [], 'error' => null];
        } catch (\Throwable $e) {
            return $this->err($e);
        }
    }

    public function deleteStudent(string $id): array
    {
        try {
            $s = $this->students->find(new StudentId($id));
            if ($s) $this->students->delete($s);
            return ['status' => 200, 'data' => [], 'error' => null];
        } catch (\Throwable $e) {
            return $this->err($e);
        }
    }

    // ── Teachers ─────────────────────────────────────────────────────────────

    public function getTeachers(): array
    {
        try {
            $all = $this->teachers->findAll();
            return ['status' => 200, 'data' => array_map([$this, 'serializeTeacher'], $all), 'error' => null];
        } catch (\Throwable $e) {
            return $this->err($e);
        }
    }

    public function getTeacher(string $id): array
    {
        try {
            $t = $this->teachers->find(new TeacherId($id));
            if (!$t) return ['status' => 404, 'data' => [], 'error' => 'Professor no trobat'];
            return ['status' => 200, 'data' => $this->serializeTeacher($t), 'error' => null];
        } catch (\Throwable $e) {
            return $this->err($e);
        }
    }

    public function createTeacher(array $data): array
    {
        try {
            $id = 'teacher_' . uniqid('', true);
            $handler = new CreateTeacherHandler($this->teachers);
            $handler->handle(new CreateTeacherCommand($id, $data['name'], $data['specialty'] ?? ''));
            return ['status' => 201, 'data' => [], 'error' => null];
        } catch (\Throwable $e) {
            return $this->err($e);
        }
    }

    public function updateTeacher(string $id, array $data): array
    {
        try {
            $handler = new UpdateTeacherHandler($this->teachers);
            $handler->handle(new UpdateTeacherCommand($id, $data['name'], $data['specialty'] ?? ''));
            return ['status' => 200, 'data' => [], 'error' => null];
        } catch (\Throwable $e) {
            return $this->err($e);
        }
    }

    public function deleteTeacher(string $id): array
    {
        try {
            $t = $this->teachers->find(new TeacherId($id));
            if ($t) $this->teachers->delete($t);
            return ['status' => 200, 'data' => [], 'error' => null];
        } catch (\Throwable $e) {
            return $this->err($e);
        }
    }

    // ── Subjects ─────────────────────────────────────────────────────────────

    public function getSubjects(): array
    {
        try {
            $all = $this->subjects->findAll();
            return ['status' => 200, 'data' => array_map([$this, 'serializeSubject'], $all), 'error' => null];
        } catch (\Throwable $e) {
            return $this->err($e);
        }
    }

    public function getSubject(string $id): array
    {
        try {
            $s = $this->subjects->find(new SubjectId($id));
            if (!$s) return ['status' => 404, 'data' => [], 'error' => 'Assignatura no trobada'];
            return ['status' => 200, 'data' => $this->serializeSubject($s), 'error' => null];
        } catch (\Throwable $e) {
            return $this->err($e);
        }
    }

    public function createSubject(array $data): array
    {
        try {
            $id = 'subject_' . uniqid('', true);
            // CreateSubjectHandler necessita SubjectRepository + CourseRepository

            $courses = new \App\Infrastructure\Persistence\Doctrine\DoctrineCourseRepository($this->em);
            $handler = new CreateSubjectHandler($this->subjects, $courses);
            $handler->handle(new CreateSubjectCommand($id, $data['name'], $data['courseId']));
            return ['status' => 201, 'data' => [], 'error' => null];
        } catch (\Throwable $e) {
            return $this->err($e);
        }
    }

    public function updateSubject(string $id, array $data): array
    {
        try {
            $handler = new UpdateSubjectHandler($this->subjects);
            $handler->handle(new UpdateSubjectCommand($id, $data['name']));
            return ['status' => 200, 'data' => [], 'error' => null];
        } catch (\Throwable $e) {
            return $this->err($e);
        }
    }

    public function deleteSubject(string $id): array
    {
        try {
            $s = $this->subjects->find(new SubjectId($id));
            if ($s) $this->subjects->delete($s);
            return ['status' => 200, 'data' => [], 'error' => null];
        } catch (\Throwable $e) {
            return $this->err($e);
        }
    }

    public function assignTeacherToSubject(string $subjectId, string $teacherId): array
    {
        try {
            $handler = new AssignTeacherToSubjectHandler(
                $this->teachers,
                $this->subjects,
                $this->assignments
            );
            $handler->handle(new AssignTeacherToSubjectCommand($teacherId, $subjectId));
            return ['status' => 200, 'data' => [], 'error' => null];
        } catch (\Throwable $e) {
            return $this->err($e);
        }
    }
    // ── Courses ──────────────────────────────────────────────────────────────────

    public function getCourses(): array
    {
        try {
            $courses = $this->em->getRepository(\App\Domain\Course\Course::class)->findAll();
            return ['status' => 200, 'data' => array_map(fn($c) => [
                'id'   => $c->id()->value(),
                'name' => $c->name(),
            ], $courses), 'error' => null];
        } catch (\Throwable $e) {
            return $this->err($e);
        }
    }

    // ── Serializers ──────────────────────────────────────────────────────────

    private function serializeStudent(\App\Domain\Student\Student $s): array
    {
        return [
            'id'    => $s->id()->value(),
            'name'  => $s->name(),
            'email' => $s->email()->value(),
        ];
    }

    private function serializeTeacher(\App\Domain\Teacher\Teacher $t): array
    {
        return [
            'id'        => $t->id()->value(),
            'name'      => $t->name(),
            'specialty' => $t->specialty(),
        ];
    }

    private function serializeSubject(\App\Domain\Subject\Subject $s): array
    {
        return [
            'id'        => $s->id()->value(),
            'name'      => $s->name(),
            'courseId'  => $s->courseId()->value(),
            'teacherId' => $s->teacherId()?->value(),
        ];
    }

    // ── Error helper ─────────────────────────────────────────────────────────

    private function err(\Throwable $e): array
    {
        return ['status' => 500, 'data' => [], 'error' => $e->getMessage()];
    }
}
