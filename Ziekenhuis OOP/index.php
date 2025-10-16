<?php
declare(strict_types=1);

require __DIR__ . '/src/Person.php';
require __DIR__ . '/src/Patient.php';
require __DIR__ . '/src/Staff.php';
require __DIR__ . '/src/Doctor.php';
require __DIR__ . '/src/Nurse.php';
require __DIR__ . '/src/Appointment.php';

use Hospital\{Patient, Doctor, Nurse, Appointment};

function euro(float $n): string {
    return '€' . number_format($n, 2, ',', '.');
}

// demo-objecten
$patient = new Patient('Alice Janssen',);
$patient->setPaymentPerHour(175.00);

$doctor = new Doctor('Dr. Berg');
$doctor->setSalary(120.00); // uurloon

$nurse1 = new Nurse('Kim de Vries');
$nurse1->setSalary(820.00);     // vast weekloon
$nurse1->setBonusHourlyRate(15.00);

$nurse2 = new Nurse('Mohamed Ali');
$nurse2->setSalary(820.00);
$nurse2->setBonusHourlyRate(12.50);

// afspraken (doctor + patient, nurses optioneel)
$appt1 = new Appointment(
    $patient,
    $doctor,
    new \DateTimeImmutable('2025-10-13 09:00'),
    new \DateTimeImmutable('2025-10-13 10:30'),
    [$nurse1]
);

$appt2 = new Appointment(
    $patient,
    $doctor,
    new \DateTimeImmutable('2025-10-15 14:00'),
    new \DateTimeImmutable('2025-10-15 15:00'),
    [$nurse2]
);
$appt2->addNurse($nurse1);

// eenvoudige output met getters
echo "<h2>Appointments: " . Appointment::getCount() . "</h2>";
foreach (Appointment::all() as $a) {
    $nurseNames = array_map(fn($n) => $n->getName(), $a->getNurses());
    echo "<p>";
    echo "Patient: " . $a->getPatient()->getName() . "<br>";
    echo "Doctor: "  . $a->getDoctor()->getName()  . "<br>";
    echo "Begin: "   . $a->getBeginTime() . "<br>";
    echo "End: "     . $a->getEndTime()   . "<br>";
    echo "Duration (h): " . number_format($a->getTimeDifference(), 2) . "<br>";
    echo "Nurses: " . ($nurseNames ? implode(', ', $nurseNames) : '—') . "<br>";
    echo "Costs: " . euro($a->getCosts());
    echo "</p>";
}

echo "<hr>";
echo "<h3>Salaries (week)</h3>";
echo "<p>Doctor " . $doctor->getName() . ": " . euro($doctor->getSalary()) . "</p>";
echo "<p>Nurse "  . $nurse1->getName()  . ": " . euro($nurse1->getSalary()) . "</p>";
echo "<p>Nurse "  . $nurse2->getName()  . ": " . euro($nurse2->getSalary()) . "</p>";
