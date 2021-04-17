<?php

namespace App\Main\Users\CaseUses;

use App\Main\Users\Domain\WhereUserDomain;
use App\Utils\GeneratePassword;

class IsValidReferenceCodeForUserCaseUse
{
    public function __invoke()
    {
        $generatePasswordObj = new GeneratePassword();
        $whereUserObj = new WhereUserDomain();
        $referenceCode = ($generatePasswordObj)(env('LENGTH_REFERENCE_CODE'), false, 'lud');
        $users = ($whereUserObj)(['reference_code' => $referenceCode]);
        if ($users->count() <= 0) {
            return $referenceCode;
        }
        $referenceCode = (new GeneratePassword())(env('LENGTH_REFERENCE_CODE') + 1, false, 'lud');
        $users = ($whereUserObj)(['reference_code' => $referenceCode]);
        if ($users->count() > 0) {
            return '';
        }

        return $referenceCode;
    }
}
