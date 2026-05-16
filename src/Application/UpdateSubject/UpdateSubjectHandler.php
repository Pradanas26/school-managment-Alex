<?php

namespace App\Application\UpdateSubject;

use App\Domain\Subject\Subject;
use App\Domain\Subject\SubjectId;
use App\Domain\Subject\SubjectRepository;

final class UpdateSubjectHandler
{
    public function __construct(
        private SubjectRepository $subjectRepository
    ) {}

    public function handle(UpdateSubjectCommand $command): Subject
    {
        $subject = $this->subjectRepository->find(new SubjectId($command->subjectId));

        if ($subject === null) {
            throw new \DomainException('Subject not found: ' . $command->subjectId);
        }

        $subject->update($command->name);

        $this->subjectRepository->save($subject);

        return $subject;
    }
}
