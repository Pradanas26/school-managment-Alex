<?php

namespace App\Application\UpdateTeacher;

final class UpdateTeacherCommand
{
    public function __construct(
        public readonly string $teacherId,
        public readonly string $name,
        public readonly string $specialty
    ) {}
}
