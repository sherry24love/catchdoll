<?php
/**
 * Created by PhpStorm.
 * User: wangfan
 * Date: 2018/6/6
 * Time: 19:32
 */

namespace Sherrycin\Cms\Apis ;


use Apiato\Core\Abstracts\Controllers\ApiController;
use Sherrycin\Cms\Models\Advertisement ;
use Illuminate\Http\Request;
use Sherrycin\Cms\Models\Advtarget;

class AdvertisementApi extends ApiController
{

    public function __construct()
    {

        $this->middleware('auth:api')->only([

        ]);
    }


    /**
     * 列表
     */
    public function index( Request $request ) {
        $target = $request->input('target') ;

        $query = Advertisement::where('display' , 1 );

        //如果有提供广告位
        if( $target ) {
            $advTarget = Advtarget::where('slug' , $target )->first();
            $query = $query->where('target_id' , data_get( $advTarget , 'id' , 0 )) ;
        }

        $now = time();

        $pagesize = $request->input('pagesize' , 5 );

        $list = $query->orderBy('sort' , 'asc' )->take( $pagesize )->get();
        if( $list->isNotEmpty() ) {
            $list = $list->toArray();
            foreach( $list as $k => $val ) {
                $list[ $k ]['cover'] = \Storage::disk('public')->url( $val['cover'] ) ;
            }
        }
        return response()->json([
            'errcode' => 0 ,
            'list' => $list
        ]);
    }


    public function store() {

    }


    public function update() {

    }

    public function show() {

    }


    public function destroy() {

    }
}