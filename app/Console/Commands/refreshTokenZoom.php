<?php

namespace App\Console\Commands;

use App\Main\Config_System\Domain\SearchConfigDomain;
use App\Main\Config_System\Domain\UpdateConfigDomain;
use App\Main\Config_System\UseCases\SearchConfigurationUseCase;
use App\Main\Config_System\UseCases\UpdateConfigSystemUseCase;
use App\Utils\ZoomToken;
use Illuminate\Console\Command;

class refreshTokenZoom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:refreshzoom';

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
        try {
            // Generate token
            $zoom = new ZoomToken();
            $token = $zoom->build();
            // Save in Database
            $update = new UpdateConfigSystemUseCase(new UpdateConfigDomain());
            // Search key
            $search = new SearchConfigurationUseCase(new SearchConfigDomain());
            $config = $search->__invoke('ZOOM_ACCESS_TOKEN');
            $update->__invoke($config->id, ['value' => $token]);
        } catch (\Exception $ex) {
            $this->line('Actualizar token: '.$ex->getMessage());
        }
    }
}
