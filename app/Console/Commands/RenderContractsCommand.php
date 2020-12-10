<?php

namespace App\Console\Commands;

use App\Main\Cases\CasesUses\CreatePDFContractCaseUse;
use App\Main\Cases\CasesUses\TemplateContractCasesUse;
use App\Main\Cases\Domain\CasesJoinCustomerDomain;
use Illuminate\Console\Command;

class RenderContractsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:contracts';

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
        $packages = (new CasesJoinCustomerDomain())(['cases.url_doc' => '']);
        foreach ($packages->toArray() as $key => $item) {
            $obj = (object) $item;
            \Log::error((print_r($obj, true)));
            $view = (new TemplateContractCasesUse())($obj);
            $namefile = preg_replace(
                '/[^A-Za-z0-9\-]/', '',
                uniqid($obj->packages_id.$obj->services_id.$obj->customer_id.date('Ymdhis'))).'.pdf';
            \Log::error('Nombre archivo: '.$namefile);
            // Create and save PDF
            (new CreatePDFContractCaseUse())(
                $view['layout'],
                $namefile,
                storage_path('contracts/'));
        }
    }
}
