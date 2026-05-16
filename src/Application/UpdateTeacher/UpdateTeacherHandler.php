<?php

namespace App\Application\UpdateTeacher;

use App\Domain\Teacher\Teacher;
use App\Domain\Teacher\TeacherId;
use App\Domain\Teacher\TeacherRepository;

final class UpdateTeacherHandler
{
    public function __construct(
        private TeacherRepository $teacherRepository
    ) {}

    public function handle(UpdateTeacherCommand $command): Teacher
    {
        $teacher = $this->teacherRepository->find(new TeacherId($command->teacherId));

        if ($teacher === null) {
            throw new \DomainException('Teacher not found: ' . $command->teacherId);
        }

        $teacher->update($command->name, $command->specialty);

        $this->teacherRepository->save($teacher);

        return $teacher;
    }
}
