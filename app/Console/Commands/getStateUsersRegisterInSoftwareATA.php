<?php

namespace App\Console\Commands;

use App\Main\Users\Domain\FindUserByIdDomain;
use App\Main\Users\Services\SynchronizedUserExternalServices;
use Illuminate\Console\Command;

/**
 * Command for verify synchronize user with ATA.
 */
class getStateUsersRegisterInSoftwareATA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getstateuser:softwareata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $response = (new SynchronizedUserExternalServices())();
        $resultarray = \collect(explode('<br>', $response) ?? []);
        \Log::error(date('Y-m-d H:i:s .-.').'Sincronización de usuarios con software ATA');
        \Log::error('Datos de entrada: '.print_r($response, 1));
        if ($resultarray->count() <= 0) {
            throw new \Exception('Nada que actualizar', 404);
        }
        $objFindUser = new FindUserByIdDomain();
        $resultarray->each(function ($item, $key) use ($objFindUser) {
            if (!empty($item)) {
                $temp = explode(' ', $item);
                if (count($temp) > 0) {
                    $match = preg_match('/^[0-9]{0,}$/', $temp[0]);
                    if ($match) {
                        // Search by CURP in the tabla users;
                        $responseObjUser = $objFindUser(['curp' => $temp[1]]);
                        // Update users set id ext software ata
                        if (!is_null($responseObjUser)) {
                            if (is_null($responseObjUser->id_ext_software_ata)) {
                                $responseObjUser->id_ext_software_ata = $temp[0];
                                $responseObjUser->save();
                                \Log::error('Datos actualizados: '.$responseObjUser->curp.'- id externo: '.$temp[0]);
                            }
                        }
                    }
                }
            }
        });
    }

    public function mockupResponse()
    {
        return '201 EDUARDOKMSNAN<br>202 VERONICARICHARS<br>203 AIDE323232USBNADB<br>204 CMPRUEBA1212187<br>205 ABOGADO1212121<br>206 CALLCENTER121212<br>207 AMANITA123212121<br>208 CURP225648779<br>';
    }
}
