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
use Sherrycin\Video\Models\Collect;
use Sherrycin\Video\Models\Video ;
use Sherrycin\Video\Transformers\VideoShowTransformer;
use Sherrycin\Video\Transformers\VideoTransformer ;

class VideoApi extends ApiController
{

    public function __construct()
    {

        $this->middleware('auth:api')->only([
            'store' , 'show'
        ]);
    }


    /**
     * 列表   获取视频信息
     */
    public function index( Request $request )
    {
        $query = Video::with('user')->where('status' , 1 )->orderBy('sort' , 'asc' )->orderBy('id' , 'desc' );
        $myself = $request->input('myself' ,  0 );
        if( $myself ) {
            $user = auth()->guard('api')->user();
            $query = $query->where('user_id' , $user->id );
        }
        $list = $query->paginate( 10 );

        $video = $this->transform( $list , VideoTransformer::class , [] , [] , 'id' );
        return response()->json( [
            'errcode' => 0 ,
            'data' => $video ,
        ] );
    }

    /**
     * @param Request $request
     * 获取token信息
     */
    public function conf( Request $request ) {
        $disk = config('filesystems.disks.public') ;
        $auth = new Auth( data_get( $disk , 'access_key') , data_get( $disk , 'secret_key' ) ) ;
        $expires = 7200;
        $bucket = data_get( $disk , 'bucket') ;
        $upToken = $auth->uploadToken($bucket, null, $expires, null , true);
        return response()->json( ['uptoken' => $upToken ] );
    }

    /**
     * 创建视频
     */
    public function store( Request $request ) {
        $user = auth()->guard('api')->user();
        $title = $request->input('title');
        $desc = $request->input('desc' , '' ) ;
        $url = $request->input('url');
        $size = $request->input('size' , 0 ) ;
        $width = $request->input('width') ;
        $height = $request->input('height' ) ;
        $video = Video::create([
            'user_id' => $user->id ,
            'url' => $url ,
            'title' => $title ,
            'desc' => $desc ,
            'status' => 1 ,
            'size' => $size ,
            'width' => $width ,
            'height' => $height ,
        ]);
        if( $video ) {
            return response()->json([
                'errcode' => 0 ,
                'msg' => '上传成功'
            ]);
        }
        return response()->json([
            'errcode' => 10001 ,
            'msg' => '上传失败'
        ]);

    }


    public function update() {

    }

    public function show( $id ) {
        $video = Video::withCount('comment' , 'collect')->findOrFail( $id );
        $user = auth()->guard('api')->user();
        $collect = Collect::where('video_id' , $id )->where('user_id' , $user->id )->count() ;

        return response()->json([
            'errcode' => 0 ,
            'data' => $this->transform( $video , VideoShowTransformer::class , [] , [] , 'id' ) ,
            'collect' => $collect
        ]);
    }


    public function destroy() {

    }
}