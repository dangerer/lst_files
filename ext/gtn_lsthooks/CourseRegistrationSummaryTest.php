<?php

declare(strict_types=1);

use Gtn\GtnLsthooks\Service\CourseRegistrationSummary;

require_once __DIR__ . '/../../Classes/Service/CourseRegistrationSummary.php';

function answerRows(int $uid, array $fields): array
{
    $rows = [];
    foreach ($fields as $marker => $value) {
        $rows[] = ['uid' => $uid, 'marker' => $marker, 'value' => $value];
    }
    return $rows;
}

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$summary = new CourseRegistrationSummary();
check($summary->summarize([]) === [], 'Empty input must produce an empty list.');
$oldTerm = '19.10.2025 09:00 bis 20.10.2025 16:30';
$newTerm = '19.10.2027 09:00 bis 20.10.2027 16:30';
$rows = array_merge(
    answerRows(1, [
        'kursuid' => '42', 'kurstitel' => 'Training', 'termin' => $oldTerm, 'courseid' => 'A1',
        'selbstfuerkursanmelden' => '1', 'vorname_01' => '["Anna","","Ben"]',
    ]),
    answerRows(2, [
        'kursuid' => '42', 'kurstitel' => 'Training aktualisiert', 'termin' => $oldTerm,
        'selbstfuerkursanmelden' => '0', 'vorname_01' => '{"3":"Clara","7":""}',
    ]),
    answerRows(3, [
        'kursuid' => '42', 'termin' => $newTerm, 'selbstfuerkursanmelden' => '1',
    ]),
    answerRows(4, [
        'kursuid' => '43', 'termin' => $newTerm, 'vorname_01' => 'invalid json',
    ]),
    answerRows(5, ['kurstitel' => 'No course UID', 'selbstfuerkursanmelden' => '1']),
    answerRows(6, ['kursuid' => '44', 'selbstfuerkursanmelden' => '1'])
);
$result = $summary->summarize((static function () use ($rows): Generator {
    yield from $rows;
})());
check(count($result) === 4, 'Group by UID and term; skip mails without a course UID.');
check($result[0]['participants'] === 4, 'Count self and additional people, not mails or empty names.');
check($result[0]['kurstitel'] === 'Training aktualisiert', 'Use the latest saved title.');
check($result[0]['courseid'] === 'A1', 'An absent later label must not erase the existing label.');
check($result[1]['participants'] === 1, 'A new date for the same DCE must stay separate.');
check($result[2]['participants'] === 0, 'Invalid JSON must not invent participants.');
check($result[3]['termin'] === '' && $result[3]['participants'] === 1, 'Keep registrations without dates, including the last streamed mail.');
echo "CourseRegistrationSummary: all checks passed.\n";
