<?php

namespace App\Http\Controllers;

use App\Models\Words;
use Illuminate\Http\Request;
use Sherrycin\Catchdoll\Catchdoll;

class VideosController extends Controller
{
    //
    public function index() {
        echo __LINE__ ;
        $xs = new \XS( config_path( 'xunsearch.ini') ) ;
        $tokenizer = new \XSTokenizerScws() ;
        //$tokenizer->setMulti( 2 );

        $text = '泛彼柏舟，亦泛其流。耿耿不寐，如有隐忧。微我无酒，以敖以游。我心匪鉴，不可以茹。亦有兄弟，不可以据。薄言往诉，逢彼之怒。我心匪石，不可转也。我心匪席，不可卷也。威仪棣棣，不可选也。忧心悄悄，愠于群小。觏闵既多，受侮不少。静言思之，寤辟有摽。日居月诸，胡迭而微？心之忧矣，如匪浣衣。静言思之，不能奋飞。';
        $words = $tokenizer->getTops($text , 20 , 'a,ad,an,n,z,nr,vn');
        if( is_array( $words ) && !empty( $words ) ) {
            foreach( $words as $word ) {
                echo data_get( $word , 'attr') ;
                Words::firstOrCreate([
                    'word' => trim( data_get( $word , 'word' ))
                ] , [
                    'from' => '诗经' ,
                    'property' => trim( data_get( $word , 'attr' )) ,
                    'weight' => trim( data_get( $word , 'times' , 0 ))
                ]) ;
            }
        }

    }
}
