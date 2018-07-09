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
use Sherrycin\Video\Models\Comment;
use Sherrycin\Video\Models\Video ;
use Sherrycin\Video\Transformers\VideoShowTransformer;
use Sherrycin\Video\Transformers\VideoTransformer ;

class CommentApi extends ApiController
{

    public function __construct()
    {

        $this->middleware('auth:api')->only([
            'store'
        ]);
    }


    /**
     * 列表   获取视频信息
     */
    public function index( $id ,  Request $request )
    {
        $query = Comment::with('user')->where('video_id' , $id )->where('status' , 0 )->orderBy('id' , 'desc' ) ;

        $list = $query->paginate( 10 );
        return response()->json( [
            'errcode' => 0 ,
            'data' => $list ,
        ] );
    }


    /**
     * 创建视频
     */
    public function store( $id , Request $request ) {
        $user = auth()->guard('api')->user();
        $content = $request->input('content');
        $video = Comment::create([
            'user_id' => $user->id ,
            'video_id' => $id ,
            'content' => $content ,
        ]);
        if( $video ) {
            return response()->json([
                'errcode' => 0 ,
                'msg' => '评论完成'
            ]);
        }
        return response()->json([
            'errcode' => 10001 ,
            'msg' => '评论失败'
        ]);

    }


    public function update() {

    }

    public function show( $id ) {
        $video = Video::withCount('comment')->findOrFail( $id );
        return response()->json([
            'errcode' => 0 ,
            'data' => $this->transform( $video , VideoShowTransformer::class , [] , [] , 'id' )
        ]);
    }


    public function destroy() {

    }
}