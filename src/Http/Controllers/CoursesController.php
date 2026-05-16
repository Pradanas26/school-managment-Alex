<?php

namespace App\Http\Controllers;

use App\Http\Request;
use App\Http\ResponseJson;
use App\Domain\Course\Course;
use App\Domain\Course\CourseId;
use Doctrine\ORM\EntityManagerInterface;

class CoursesController
{
    private EntityManagerInterface $em;

    public function __construct(private Request $request, EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    // GET /api/courses
    public function index(): void
    {
        $courses = $this->em->getRepository(Course::class)->findAll();

        $data = array_map(fn($c) => [
            'id'   => $c->id()->value(),
            'name' => $c->name(),
            'year' => $c->year(),
        ], $courses);

        (new ResponseJson(200, $data))->send();
    }

    // GET /api/courses/{id}
    public function show(string $id): void
    {
        $course = $this->em->getRepository(Course::class)->find($id);

        if ($course === null) {
            (new ResponseJson(404, ['error' => 'Course not found']))->send();
            return;
        }

        (new ResponseJson(200, [
            'id'   => $course->id()->value(),
            'name' => $course->name(),
            'year' => $course->year(),
        ]))->send();
    }

    // POST /api/courses
    public function create(): void
    {
        try {
            $body = $this->request->getBody();

            $id   = CourseId::generate()->value();
            $name = $body['name'] ?? '';
            $year = (int) ($body['year'] ?? date('Y'));

            $course = new Course(new CourseId($id), $name, $year);

            $this->em->persist($course);
            $this->em->flush();

            (new ResponseJson(201, [
                'id'   => $id,
                'name' => $name,
                'year' => $year,
            ]))->send();

        } catch (\InvalidArgumentException $e) {
            (new ResponseJson(422, ['error' => $e->getMessage()]))->send();
        } catch (\Throwable $e) {
            (new ResponseJson(500, ['error' => 'Internal server error: ' . $e->getMessage()]))->send();
        }
    }

    // DELETE /api/courses/{id}
    public function delete(string $id): void
    {
        try {
            $course = $this->em->getRepository(Course::class)->find($id);

            if ($course === null) {
                (new ResponseJson(404, ['error' => 'Course not found']))->send();
                return;
            }

            $this->em->remove($course);
            $this->em->flush();

            (new ResponseJson(200, ['message' => 'Course deleted']))->send();

        } catch (\Throwable $e) {
            (new ResponseJson(500, ['error' => 'Internal server error: ' . $e->getMessage()]))->send();
        }
    }
}
