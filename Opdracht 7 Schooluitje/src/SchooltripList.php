<?php
declare(strict_types=1);

namespace App;

use App\Models\Student;
use App\Models\Teacher;

class SchooltripList
{
    private array $students = [];
    private array $teachers = [];

    public function addStudent(Student $student): void
    {
        $this->students[] = $student;
    }

    public function addTeacher(Teacher $teacher): void
    {
        $this->teachers[] = $teacher;
    }

    public function getStudents(): array
    {
        return $this->students;
    }

    public function getTeachers(): array
    {
        return $this->teachers;
    }

    public function getPayingStudents(): array
    {
        return array_values(array_filter($this->students, fn(Student $s) => $s->getPaid()));
    }

    public function getEligibleTeacherCount(): int
    {
        return intdiv(count($this->getPayingStudents()), 5);
    }

    public function getAssignedTeachers(): array
    {
        $n = min($this->getEligibleTeacherCount(), count($this->teachers));
        return array_slice($this->teachers, 0, $n);
    }

    public function makeGroups(int $groupSize = 5): array
    {
        $paying = $this->getPayingStudents();
        $teachers = $this->getAssignedTeachers();
        $groups = [];
        $p = 0;
        $t = 0;
        while ($p < count($paying)) {
            $chunk = array_slice($paying, $p, $groupSize);
            $teacher = $t < count($teachers) ? $teachers[$t] : null;
            $groups[] = ['students' => $chunk, 'teacher' => $teacher];
            $p += $groupSize;
            if ($teacher) {
                $t++;
            }
        }
        return $groups;
    }

    public function getTotalCollected(): int
    {
        $sum = 0;
        foreach ($this->students as $s) {
            $sum += $s->getFeePaid();
        }
        return $sum;
    }

    public function getTotalCollectedByClass(): array
    {
        $totals = [];
        foreach ($this->students as $s) {
            $c = $s->getClassName();
            if (!isset($totals[$c])) {
                $totals[$c] = 0;
            }
            $totals[$c] += $s->getFeePaid();
        }
        ksort($totals);
        return $totals;
    }

    public function getClassStats(): array
    {
        $map = [];
        foreach ($this->students as $s) {
            $c = $s->getClassName();
            if (!isset($map[$c])) {
                $map[$c] = ['total' => 0, 'signed' => 0, 'paid' => 0];
            }
            $map[$c]['total']++;
            if ($s->getSignedUp()) {
                $map[$c]['signed']++;
            }
            if ($s->getPaid()) {
                $map[$c]['paid']++;
            }
        }
        foreach ($map as $c => $v) {
            $total = $v['total'] ?: 1;
            $map[$c]['percentSigned'] = ($v['signed'] / $total) * 100;
            $map[$c]['percentPaid'] = ($v['paid'] / $total) * 100;
        }
        ksort($map);
        return $map;
    }
}
