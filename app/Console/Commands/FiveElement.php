<?php

namespace App\Console\Commands;

use App\Models\Characters;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FiveElement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fiveElement:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '汉字五行汇总';

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
        $file = Storage::path('public/wuxing');
        $this->info( $file );
        $fp = fopen( $file  , 'r' ) ;
        $strokes = 0 ;
        $fiveElement = "" ;



        $map = [] ;
        while (($line = fgets( $fp )) !== false) {
            if( mb_strlen( $line ) < 10 ) {
                $strokes = $this->covnertNum( str_replace("画" , "" , $line ) ) ;
            }
            if( preg_match("/：/" , $line ) ) {
                preg_match("/“(.+)”/" , $line , $match );

                if( !empty( $match ) ) {
                    $fiveElement = data_get( $match , 1 );
                }

                //当前五行也有了
                $array = explode("：" , $line );
                $this->info( data_get( $array , 1 ) ) ;
                foreach( str_split( trim( data_get( $array , 1 ) ) , 3 ) as $val ) {
                    /**
                    $character = Characters::create([
                        'character' => $val ,
                        'strokes' => $strokes ,
                        'five_element' => $fiveElement ,
                        'weight' => 0 ,
                    ]);

                     **/
                    $this->info( var_export( [
                        'character' => $val ,
                        'strokes' => $strokes ,
                        'five_element' => $fiveElement ,
                        'weight' => 0 ,
                    ] , true ) );
                }
            }
        }

    }


    protected function covnertNum ( $str ) {
        $map = array(
            '一' => '1','二' => '2','三' => '3','四' => '4','五' => '5','六' => '6','七' => '7','八' => '8','九' => '9',
            '壹' => '1','贰' => '2','叁' => '3','肆' => '4','伍' => '5','陆' => '6','柒' => '7','捌' => '8','玖' => '9',
            '零' => '0','两' => '2',
            '仟' => '千','佰' => '百','拾' => '十',
            '万万' => '亿',
        );

        $str = str_replace(array_keys($map), array_values($map), $str );
        $str = $this->checkString($str, '/([\d亿万千百十]+)/u');

        $func_c2i = function ($str, $plus = false) use(&$func_c2i) {
            if(false === $plus) {
                $plus = array('亿' => 100000000,'万' => 10000,'千' => 1000,'百' => 100,'十' => 10,);
            }

            $i = 0;
            if($plus)
                foreach($plus as $k => $v) {
                    $i++;
                    if(strpos($str, $k) !== false) {
                        $ex = explode($k, $str, 2);
                        $new_plus = array_slice($plus, $i, null, true);
                        $l = $func_c2i($ex[0], $new_plus);
                        $r = $func_c2i($ex[1], $new_plus);
                        if($l == 0) $l = 1;
                        return $l * $v + $r;
                    }
                }

            return (int)$str;
        };

        return $func_c2i($str);
    }

    protected function checkString($var, $check = '', $default = '') {
        if (!is_string($var)) {
            if(is_numeric($var)) {
                $var = (string)$var;
            }
            else {
                return $default;
            }
        }
        if ($check) {
            return (preg_match($check, $var, $ret) ? $ret[1] : $default);
        }

        return $var;
    }

}
