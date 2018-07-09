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

class VideoTransformer extends Transformer {


    /**
     * @param $token
     *
     * @return  array
     */
    public function transform($data )
    {
        $list = [
            'id' => data_get( $data , 'id') ,
            'url' => data_get( $data , 'url' ) ,
            'cover' => data_get( $data , 'cover' ) . '?vframe%2fjpg%2foffset%2f7' ,
            'title' => data_get( $data , 'title' ) ,
            'user' => data_get( $data , 'user' ) ,
            'created_at' => (string) data_get( $data , 'created_at' )
        ] ;
        return $list;
    }

}