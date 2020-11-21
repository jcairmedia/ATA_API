<?php

namespace App\Main\Meetings\UseCases;

use App\Main\Meetings\Domain\MeetingListDomain;

class MeetingListUseCase
{
    public function __invoke(string $filter, int $index, int $byPage = 10, array $config = [])
    {
        $list = new MeetingListDomain();

        return $list($filter, $index, $byPage, $config);
    }
}
