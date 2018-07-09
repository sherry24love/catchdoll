<?php

namespace App\Console\Commands;

use App\Models\Characters;
use App\Models\Words;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class Poem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wordimport:poem';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '诗经单调导入';

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
        $file = Storage::path('public/poem');
        $this->info( $file );
        $fp = fopen( $file  , 'r' ) ;
        $strokes = 0 ;
        $fiveElement = "" ;


        $title = "" ;
        $map = [] ;
        while (($line = fgets( $fp )) !== false) {
            if( trim( $line ) == '' ) {
                //如果是空行则把前面的全部连起来
                $poem = implode( "" , $map );
                $this->info('正在处理:' . $title );
                //$this->info( $poem );
                $xs = new \XS( config_path( 'xunsearch.ini') ) ;
                $tokenizer = new \XSTokenizerScws() ;
                //$tokenizer->setMulti( 2 );


                $words = $tokenizer->getTops( $poem , 50 , 'a,ad,an,n,z,nr,vn,vd,u');
                if( is_array( $words ) && !empty( $words ) ) {
                    foreach( $words as $word ) {
                        //$this->info( data_get( $word , 'word') );
                        Words::firstOrCreate([
                            'word' => trim( data_get( $word , 'word' ))
                        ] , [
                            'from' => "《诗经》<{$title}>[{$poem}]" ,
                            'property' => trim( data_get( $word , 'attr' )) ,
                            'weight' => trim( data_get( $word , 'times' , 0 ))
                        ]) ;
                    }
                }
                $title = "" ;
                $map = [] ;


            } else {
                if( !$title ) {
                    $title = trim( $line );
                } else {
                    $map[] = trim( $line );
                }
            }

        }

    }


}
