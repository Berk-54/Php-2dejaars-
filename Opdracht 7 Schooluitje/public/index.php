<?php
declare(strict_types=1);

namespace SchoolTrip {
    abstract class Person
    {
        private string $firstName;
        private string $lastName;
        private ?string $classGroup;

        public function __construct(string $firstName, string $lastName, ?string $classGroup = null)
        {
            $this->setFirstName($firstName);
            $this->setLastName($lastName);
            $this->setClassGroup($classGroup);
        }

        abstract public function role(): string;

        public function getFirstName(): string { return $this->firstName; }
        public function getLastName(): string { return $this->lastName; }
        public function getFullName(): string { return $this->firstName . ' ' . $this->lastName; }
        public function getClassGroup(): ?string { return $this->classGroup; }

        public function setFirstName(string $firstName): void { $this->firstName = $firstName; }
        public function setLastName(string $lastName): void { $this->lastName = $lastName; }
        public function setClassGroup(?string $classGroup): void { $this->classGroup = $classGroup; }
    }

    class Student extends Person
    {
        private bool $paid = false;
        private float $fee;

        public function __construct(string $firstName, string $lastName, string $classGroup, float $fee)
        {
            parent::__construct($firstName, $lastName, $classGroup);
            $this->setFee($fee);
        }

        public function role(): string { return 'Student'; }

        public function isPaid(): bool { return $this->paid; }
        public function getFee(): float { return $this->fee; }

        public function setPaid(bool $paid): void { $this->paid = $paid; }
        public function setFee(float $fee): void { $this->fee = $fee; }
    }

    class Teacher extends Person
    {
        public function __construct(string $firstName, string $lastName, ?string $classGroup = null)
        {
            parent::__construct($firstName, $lastName, $classGroup);
        }

        public function role(): string { return 'Teacher'; }
    }

    class SchooltripList
    {
        private int $studentsPerTeacher;
        /** @var Person[] */
        private array $people = [];

        public static int $totalStudents = 0;
        public static int $totalPaidStudents = 0;
        public static float $totalMoney = 0.0;

        public function __construct(int $studentsPerTeacher = 5)
        {
            $this->studentsPerTeacher = $studentsPerTeacher;
        }

        public function addStudent(Student $student): void
        {
            $this->people[] = $student;
            self::$totalStudents++;

            if ($student->isPaid()) {
                self::$totalPaidStudents++;
                self::$totalMoney += $student->getFee();
            }
        }

        public function addTeacherIfAllowed(Teacher $teacher): bool
        {
            $allowed = intdiv($this->countPaidStudents(), $this->studentsPerTeacher);
            $currentTeachers = $this->countTeachers();

            if ($currentTeachers < $allowed) {
                $this->people[] = $teacher;
                return true;
            }
            return false;
        }

        /** @return Student[] */
        public function getStudents(): array
        {
            return array_values(array_filter($this->people, fn($p) => $p instanceof Student));
        }

        /** @return Teacher[] */
        public function getTeachers(): array
        {
            return array_values(array_filter($this->people, fn($p) => $p instanceof Teacher));
        }

        public function countPaidStudents(): int
        {
            $paid = 0;
            foreach ($this->getStudents() as $s) {
                if ($s->isPaid()) { $paid++; }
            }
            return $paid;
        }

        public function countTeachers(): int
        {
            return count($this->getTeachers());
        }

        public function getTotalCollectedByClass(): array
        {
            $perClass = [];
            foreach ($this->getStudents() as $s) {
                $cls = $s->getClassGroup();
                if (!isset($perClass[$cls])) {
                    $perClass[$cls] = 0.0;
                }
                if ($s->isPaid()) {
                    $perClass[$cls] += $s->getFee();
                }
            }
            ksort($perClass);
            return $perClass;
        }

        public function getParticipationByClass(): array
        {
            $perClass = [];
            foreach ($this->getStudents() as $s) {
                $cls = $s->getClassGroup();
                if (!isset($perClass[$cls])) {
                    $perClass[$cls] = ['signedUp' => 0, 'paid' => 0];
                }
                $perClass[$cls]['signedUp']++;
                if ($s->isPaid()) { $perClass[$cls]['paid']++; }
            }

            foreach ($perClass as $cls => $row) {
                $perClass[$cls]['percentPaid'] = $row['signedUp'] > 0
                    ? round(($row['paid'] / $row['signedUp']) * 100, 1)
                    : 0.0;
            }

            ksort($perClass);
            return $perClass;
        }

        public function printTripList(): void
        {
            $teachers = $this->getTeachers();
            $studentsPaid = array_values(array_filter($this->getStudents(), fn($s) => $s->isPaid()));
            $studentsUnpaid = array_values(array_filter($this->getStudents(), fn($s) => !$s->isPaid()));
            $students = array_merge($studentsPaid, $studentsUnpaid);

            $neededTeachers = intdiv($this->countPaidStudents(), $this->studentsPerTeacher);
            $teachers = array_slice($teachers, 0, $neededTeachers);

            echo "=== School Trip List (1 teacher per {$this->studentsPerTeacher} PAID students) ===\n";
            echo "Allowed teachers: {$neededTeachers}\n\n";

            $teacherIndex = 0;
            $paidCounter = 0;

            foreach ($students as $student) {
                if ($student->isPaid()) {
                    if ($paidCounter % $this->studentsPerTeacher === 0 && isset($teachers[$teacherIndex])) {
                        $t = $teachers[$teacherIndex];
                        echo "- Teacher: {$t->getFullName()}";
                        $cg = $t->getClassGroup();
                        echo $cg ? " (Class: {$cg})" : "";
                        echo " | Paid: n/a\n";
                        $teacherIndex++;
                    }
                    $paidCounter++;
                }

                $paidTxt = $student->isPaid() ? 'yes' : 'no';
                echo "  • Student: {$student->getFullName()} (Class: {$student->getClassGroup()}) | Paid: {$paidTxt}\n";
            }

            echo "\n--- Totals ---\n";
            echo "Students (in list): " . count($this->getStudents()) . "\n";
            echo "Paid students: " . $this->countPaidStudents() . "\n";
            echo "Teachers (allowed/added): " . count($teachers) . "\n";
            echo "Collected total: €" . number_format(self::$totalMoney, 2, ',', '.') . "\n\n";

            echo "--- Per class: participation & collected ---\n";
            $participation = $this->getParticipationByClass();
            $byClassMoney = $this->getTotalCollectedByClass();

            foreach ($participation as $cls => $row) {
                $money = $byClassMoney[$cls] ?? 0.0;
                echo "Class {$cls}: signed up {$row['signedUp']}, paid {$row['paid']} ({$row['percentPaid']}%), collected €" .
                    number_format($money, 2, ',', '.') . "\n";
            }
        }
    }
}

namespace {
    use SchoolTrip\{Student, Teacher, SchooltripList};

    // Zet voor test op 3; voor productie 5
    $list = new SchooltripList(3);
    $fee = 40.00;

    // Studenten
    $st1 = new Student('Liam', 'Vermeer', 'SD2A', $fee);  $st1->setPaid(true);
    $st2 = new Student('Noah', 'Peeters', 'SD2A', $fee);  $st2->setPaid(false);
    $st3 = new Student('Emma', 'Koster', 'SD2A', $fee);   $st3->setPaid(true);
    $st4 = new Student('Mila', 'Bos', 'SD2B', $fee);      $st4->setPaid(true);
    $st5 = new Student('Lucas', 'Jansen', 'SD2B', $fee);  $st5->setPaid(false);
    $st6 = new Student('Sophie', 'Visser', 'SD2B', $fee); $st6->setPaid(true);

    $list->addStudent($st1);
    $list->addStudent($st2);
    $list->addStudent($st3);
    $list->addStudent($st4);
    $list->addStudent($st5);
    $list->addStudent($st6);

    // Docenten
    $tc1 = new Teacher('Mark', 'de Vries', 'SD2A');
    $tc2 = new Teacher('Eva', 'Hendriks', 'SD2B');
    $tc3 = new Teacher('Tom', 'Smit'); // geen klas

    $list->addTeacherIfAllowed($tc1);
    $list->addTeacherIfAllowed($tc2);
    $list->addTeacherIfAllowed($tc3);

    // Output
    $list->printTripList();

    echo "\n=== Global static counters ===\n";
    echo "Total students (all lists): " . SchooltripList::$totalStudents . "\n";
    echo "Total paid students (all lists): " . SchooltripList::$totalPaidStudents . "\n";
    echo "Total money (all lists): €" . number_format(SchooltripList::$totalMoney, 2, ',', '.') . "\n";
}
