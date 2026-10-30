<?php

declare(strict_types=1);

namespace Gtn\GtnLsthooks\Service;

final class CourseRegistrationSummary
{
    /** Answer rows must be ordered by mail uid, then answer uid. */
    public function summarize(iterable $answers): array
    {
        $groups = [];
        $mailUid = null;
        $mail = [];
        foreach ($answers as $answer) {
            $uid = (int)$answer['uid'];
            if ($mailUid !== null && $mailUid !== $uid) {
                $this->addMail($groups, $mail);
                $mail = [];
            }
            $mailUid = $uid;
            $mail[(string)$answer['marker']][] = (string)$answer['value'];
        }
        $this->addMail($groups, $mail);
        $rows = array_values($groups);
        usort($rows, static function (array $left, array $right): int {
            return strnatcasecmp($left['kursuid'], $right['kursuid'])
                ?: strcmp($left['termin'], $right['termin']);
        });
        return $rows;
    }

    private function addMail(array &$groups, array $mail): void
    {
        $courseUid = trim($mail['kursuid'][0] ?? '');
        if ($courseUid === '') {
            return;
        }
        $term = trim($mail['termin'][0] ?? '');
        // A reused DCE must not merge registrations from different dates.
        $key = serialize([$courseUid, $term]);
        if (!isset($groups[$key])) {
            $groups[$key] = [
                'kursuid' => $courseUid,
                'kurstitel' => '',
                'termin' => $term,
                'courseid' => '',
                'participants' => 0,
            ];
        }
        // Use the latest non-empty saved labels if wording changed between mails.
        foreach (['kurstitel', 'courseid'] as $marker) {
            $value = trim($mail[$marker][0] ?? '');
            if ($value !== '') {
                $groups[$key][$marker] = $value;
            }
        }
        // Match the existing registration counter: self + non-empty first names.
        foreach ($mail['selbstfuerkursanmelden'] ?? [] as $self) {
            if ($self) {
                ++$groups[$key]['participants'];
            }
        }
        foreach ($mail['vorname_01'] ?? [] as $names) {
            $groups[$key]['participants'] += count(array_filter((array)json_decode($names)));
        }
    }
}
