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

class VideoShowTransformer extends Transformer {


    /**
     * @param $token
     *
     * @return  array
     */
    public function transform($data )
    {
        $list = [
            'id' => data_get( $data , 'id') ,
            'url' => "https://" . data_get( $data , 'url' ) ,
            'title' => data_get( $data , 'title' ) ,
            'height' => data_get( $data , 'height' ) ,
            'width' => data_get( $data , 'width' ) ,
            'user' => data_get( $data , 'user' ) ,
            'collect_num' => data_get( $data , 'collect_count' , 0 ) ,
            'view_num' => data_get( $data , 'viewed', 0 ) ,
            'share_num' => data_get( $data , 'shared' , 0 ) ,
            'comment_num' => data_get( $data , 'comment_count' , 0 )
        ] ;
        return $list;
    }

}