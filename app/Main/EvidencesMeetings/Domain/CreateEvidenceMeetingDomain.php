<?php

namespace App\Main\EvidencesMeetings\Domain;

use App\EvidencesMeeting;

class CreateEvidenceMeetingDomain
{
    public function __invoke(array $data)
    {
        try {
            $evidenceObj = new EvidencesMeeting($data);
            $evidenceObj->save();

            return $evidenceObj;
        } catch (\Exception $th) {
            throw new \Exception($th->getMessage(), (int) $th->getCode());
        }
    }
}
