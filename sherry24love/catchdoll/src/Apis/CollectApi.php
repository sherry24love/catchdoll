<?php
/**
 * Created by PhpStorm.
 * User: wangfan
 * Date: 2018/6/7
 * Time: 15:05
 */

namespace Sherrycin\Catchdoll\Apis ;


use Apiato\Core\Abstracts\Controllers\ApiController;
use Illuminate\Http\Request;
use Qiniu\Auth ;
use Sherrycin\Catchdoll\Models\MachineCollect ;
use Sherrycin\Catchdoll\Models\Machine ;

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
        $lat = $request->input('lat' , 0 ) ;
        $lon = $request->input('lon' , 0 );
        $user = auth()->guard('api')->user();
        $query =  MachineCollect::with(['user' , 'machine' => function( $query ) use( $lat , $lon ) {
            return $query->select(['id' , 'cover' , 'no' , 'address' , 'score' , 'lat' , 'lon' , \DB::raw("round(6378.138*2*asin(sqrt(pow(sin(($lat*pi()/180-lat*pi()/180)/2),2)+cos($lat*pi()/180)*cos(lat*pi()/180)* pow(sin(($lon*pi()/180-lon*pi()/180)/2),2)))*1000) as distance") ]);
        } ])->where('user_id' , $user->id )->orderBy('id' , 'desc' ) ;

        $list = $query->paginate( 10 );
        return response()->json( [
            'errcode' => 0 ,
            'data' => $list ,
        ] );
    }


    /**
     * 创建收藏
     */
    public function store( $id , Request $request ) {
        $info = Machine::findOrFail($id) ;
        //更新查看次数
        $user = auth()->guard('api')->user();

        $collect = MachineCollect::firstOrCreate([
            'machine_id' => $id ,
            'user_id' => $user->id
        ] , [] );
        if( $collect ) {
            return response()->json([
                'errcode' => 0 ,
                'msg' => '收藏成功'
            ]);
        }
        return response()->json([
            'errcode' => 0 ,
            'msg' => '收藏失败'
        ]);


    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * 删除收藏
     */
    public function destroy( $id ) {
        $info = Machine::findOrFail($id) ;
        //更新查看次数
        $user = auth()->guard('api')->user();

        $row = MachineCollect::where('machine_id' , $id )->where('user_id' , $user->id )->delete();
        if( $row ) {
            return response()->json([
                'errcode' => 0 ,
                'msg' => '取消收藏成功'
            ]);
        }
        return response()->json([
            'errcode' => 0 ,
            'msg' => '取消收藏失败'
        ]);

    }
}