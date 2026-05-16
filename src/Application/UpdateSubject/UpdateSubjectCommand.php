<?php

namespace App\Application\UpdateSubject;

final class UpdateSubjectCommand
{
    public function __construct(
        public readonly string $subjectId,
        public readonly string $name
    ) {}
}
