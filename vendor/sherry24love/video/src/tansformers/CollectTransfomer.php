<?php
/**
 * Created by PhpStorm.
 * User: wangfan
 * Date: 2018/6/8
 * Time: 16:58
 */
namespace  Sherrycin\Video\Transformers ;

use Apiato\Core\Abstracts\Transformers\Transformer;
use Qiniu\Auth;

class CollectTransformer extends Transformer {


    /**
     * @param $token
     *
     * @return  array
     */
    public function transform($data )
    {
        $video = data_get( $data , 'video') ;
        $list = [
            'id' => data_get( $video , 'id') ,
            'url' => data_get( $video , 'url' ) ,
            'cover' => data_get( $video , 'cover' ) . '?vframe%2fjpg%2foffset%2f7' ,
            'title' => data_get( $video , 'title' ) ,
            'user' => data_get( $video , 'user' )
        ] ;
        return $list;
    }

}