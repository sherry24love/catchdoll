<?php
/**
 * Created by PhpStorm.
 * User: wangfan
 * Date: 2018/6/7
 * Time: 15:05
 */

namespace Sherrycin\Video\Apis ;


use Apiato\Core\Abstracts\Controllers\ApiController;
use Illuminate\Http\Request;
use Qiniu\Auth ;
use Sherrycin\Video\Models\Collect ;
use Sherrycin\Video\Models\Video ;
use Sherrycin\Video\Transformers\CollectTransformer;
use Sherrycin\Video\Transformers\VideoShowTransformer;
use Sherrycin\Video\Transformers\VideoTransformer ;

class CollectApi extends ApiController
{

    public function __construct()
    {

        $this->middleware('auth:api')->only([
            'store' , 'destroy' , 'index'
        ]);
    }


    /**
     * 列表 我的收藏
     */
    public function index( Request $request )
    {
        $user = auth()->guard('api')->user();
        $query = Collect::with('user' , 'video' )->where('user_id' , $user->id )->orderBy('id' , 'desc' ) ;

        $list = $query->paginate( 10 );
        $video = $this->transform( $list , CollectTransformer::class , [] , [] , 'id' );
        return response()->json( [
            'errcode' => 0 ,
            'data' => $video ,
        ] );
    }


    /**
     * 创建收藏
     */
    public function store( $id , Request $request ) {
        $user = auth()->guard('api')->user();
        $video = Collect::create([
            'user_id' => $user->id ,
            'video_id' => $id ,
        ]);
        if( $video ) {
            return response()->json([
                'errcode' => 0 ,
                'msg' => '收藏成功'
            ]);
        }
        return response()->json([
            'errcode' => 10001 ,
            'msg' => '收藏失败'
        ]);

    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * 删除收藏
     */
    public function destroy( $id ) {
        $user = auth()->guard('api')->user();
        $row = Collect::where('user_id' , $user->id )->where('video_id' , $id )->delete();
        if( $row ) {
            return response()->json([
                'errcode' => 0 ,
                'msg' => '删除成功'
            ]);
        }
        return response()->json([
            'errcode' => 0 ,
            'msg' => '删除失败'
        ]);

    }
}