<?php

namespace App\Application\UpdateStudent;

use App\Domain\Student\Email;
use App\Domain\Student\Student;
use App\Domain\Student\StudentId;
use App\Domain\Student\StudentRepository;

final class UpdateStudentHandler
{
    public function __construct(
        private StudentRepository $studentRepository
    ) {}

    public function handle(UpdateStudentCommand $command): Student
    {
        $student = $this->studentRepository->find(new StudentId($command->studentId));

        if ($student === null) {
            throw new \DomainException('Student not found: ' . $command->studentId);
        }

        // Mutem l'entitat existent (Doctrine tracked entity → UPDATE, no INSERT)
        $student->update($command->name, new Email($command->email));

        $this->studentRepository->save($student);

        return $student;
    }
}
