<?php

namespace App\Main\Evidencesmeetings\CaseUses;

use App\Main\EvidencesMeetings\Domain\CreateEvidenceMeetingDomain;

class CreateEvidenceMeetingCaseUse
{
    public function __invoke($meetingId)
    {
        try {
            $evidenceObj = (new CreateEvidenceMeetingDomain())([
            'meeting_id' => $meetingId,
            'folio' => preg_replace('/[^A-Za-z0-9\-\_]/', '', uniqid('', true)),
            'status' => 'UPLOAD_PENDING',
            'number_times_review' => 0,
            'time_review' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);

            return $evidenceObj;
        } catch (\Exception $ex) {
            throw new \Exception($th->getMessage(), (int) $th->getCode());
        }
    }
}
