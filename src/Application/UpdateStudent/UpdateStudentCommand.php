<?php

namespace App\Application\UpdateStudent;

final class UpdateStudentCommand
{
    public function __construct(
        public readonly string $studentId,
        public readonly string $name,
        public readonly string $email
    ) {}
}
