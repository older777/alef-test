<?php

function getHoursYear(int $year): int
{
    $startDate = new DateTime("$year-01-01 00:00:00");
    $endDate = new DateTime(($year + 1) . "-01-01 00:00:00");
    $interval = $startDate->diff($endDate);
    $totalDays = $interval->days;
    $totalHours = $totalDays * 24;

    return $totalHours;
}

function getLastUTCZone(int $currentUTC, int $flightNum, int $offset = 3): string
{
    $utc = $currentUTC;
    while ($flightNum) {
        if ($utc - $offset >= - 12) {
            $utc -= $offset;
        } else {
            $utc = 12 - abs(12 + $utc - $offset);
        }
        $flightNum--;
    }

    return (string) $utc >= 0 ? "+{$utc}" : $utc;
}

function calculateMskTime(int $year): string
{
    $startYear = 2020;
    $mskTime = new DateTime();
    $mskUTC = '3';
    $hours = 0;
    $counter = 0;
    $diff = $year - 1;

    while ($diff >= $startYear) {
        if ($diff == $startYear) {
            $hours += getHoursYear($diff) - 12;
        } else {
            $hours += getHoursYear($diff);
        }
        $diff--;
    }

    $hours -= 8;

    $flights = floor($hours / 8);
    $actualFlightHours = $hours - $hours % 8;
    $lastFlightUTC = getLastUTCZone($mskUTC, $flights, 3);
    $actualInterval = DateInterval::createFromDateString($actualFlightHours . ' hour');
    $lastFlightDT = date_add(new DateTime("2020-01-01 12:00:00"), $actualInterval)
        ->setTimezone(new DateTimeZone($lastFlightUTC));

    while (true) {
        if ($lastFlightDT->format('Y') == $year) {
            $mskTime = $lastFlightDT;
            break;
        }
        if ($counter == 0) {
            $counter += 2;
            $flights++;
            $actualFlightHours += 2;
            $lastFlightUTC = getLastUTCZone($mskUTC, $flights, 3);
        } else {
            if ($counter < 8) {
                $actualFlightHours += 1;
                $counter++;
            } else {
                $counter = 0;
            }
        }

        $actualInterval = DateInterval::createFromDateString($actualFlightHours . ' hour');
        $lastFlightDT = date_add(new DateTime("2020-01-01 12:00:00"), $actualInterval)
            ->setTimezone(new DateTimeZone($lastFlightUTC));
    }

    $mskTime = $mskTime->setTimezone(new DateTimeZone('Europe/Moscow'));

    return "Иван Иванович отметил {$year} в {$mskTime->format('Y-m-d H:i:s')} по МСК" . PHP_EOL;
}

echo calculateMskTime(2021);
echo calculateMskTime(2022);
echo calculateMskTime(2023);
echo calculateMskTime(2024);
echo calculateMskTime(2025);
echo calculateMskTime(2026);
echo calculateMskTime(2027);
echo calculateMskTime(2028);
echo calculateMskTime(2029);
echo calculateMskTime(2030);
echo calculateMskTime(2031);
echo calculateMskTime(2032);
echo calculateMskTime(2033);
echo calculateMskTime(2034);
