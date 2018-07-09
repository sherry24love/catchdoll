<?php

namespace App\Console\Commands;

use App\Models\Characters;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CharacterDescription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'character:description';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '汉字解释';

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
     * @return mixed
     */
    public function handle()
    {
        //
        $file = Storage::path('public/map');
        $this->info( $file );
        $fp = fopen( $file  , 'r' ) ;

        $map = [] ;
        $index = 0 ;
        while (($line = fgets( $fp )) !== false) {

            list( $key , $desc ) = explode(':' , $line );

            Characters::where('character', $key)->update([
                'description' => $desc
            ]);
        }
        $this->info( $index . " count not found") ;

    }

}
