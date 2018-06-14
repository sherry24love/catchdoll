<?php
/**
 * 娃娃机接口
 */
namespace Sherrycin\Catchdoll\Apis ;


use Apiato\Core\Abstracts\Controllers\ApiController;
use GuzzleHttp\Client;
use Sherrycin\Catchdoll\Models\Machine;
use Illuminate\Http\Request;
use Sherrycin\Catchdoll\Models\MachineCollect;
use Stevenyangecho\UEditor\Uploader\UploadFile;


class CatchdollApi extends ApiController {
	
	public function __construct() {
	
		$this->middleware('auth:api')->only([
				'store' , 'favor' , 'mine'
		]);
	}
	/**
	 * 列表
	 */
	public function index( Request $request ) {
		$sortBy = $request->input('sort_by') ;
		
		$query = Machine::where('status' , 1 );
		if( 'views' == $sortBy ) {
			$query = $query->orderBy('score' , 'desc' );
		}
		if( 'create_time' == $sortBy ) {
			$query = $query->orderBy('id' , 'desc' );
		}
		$lat = $request->input('lat' , 0 ) ;
		$lon = $request->input('lon' , 0 );
		$query->select(['id' , 'cover' , 'no' , 'address' , 'score' , 'lat' , 'lon' , \DB::raw("round(6378.138*2*asin(sqrt(pow(sin(($lat*pi()/180-lat*pi()/180)/2),2)+cos($lat*pi()/180)*cos(lat*pi()/180)* pow(sin(($lon*pi()/180-lon*pi()/180)/2),2)))*1000) as distance") ]);
		if( 'near' == $sortBy ) {
			$query->orderBy('distance' , 'asc');
		}
		
		$data = $query->paginate( 10 ) ;
		return response()->json([
				'errcode' => 0 ,
				'data' => $data
		]);
	}

    /**
     * @param Request $request
     * 我提交的吸烟区
     */
	public function mine( Request $request ) {
	    $user = auth()->guard('api')->user();
        $query = Machine::where('creator_id' , $user->id );
        $query = $query->orderBy('id' , 'desc' );
        $lat = $request->input('lat' , 0 ) ;
        $lon = $request->input('lon' , 0 );
        $query->select(['id' , 'cover' , 'no' , 'address' , 'status' , 'landmark' , 'created_at' , 'score' , 'lat' , 'lon' , \DB::raw("round(6378.138*2*asin(sqrt(pow(sin(($lat*pi()/180-lat*pi()/180)/2),2)+cos($lat*pi()/180)*cos(lat*pi()/180)* pow(sin(($lon*pi()/180-lon*pi()/180)/2),2)))*1000) as distance") ]);
        $data = $query->paginate( 10 ) ;
        return response()->json([
            'errcode' => 0 ,
            'data' => $data
        ]);
    }

    /**
     * 地图查看
     */
	public function map( Request $request ) {
        $url = 'http://api.map.baidu.com/';
        $data = array(
            'ak' => config('catchdoll.map_server_ak') ,
            'geotable_id' => config('catchdoll.lbs_table_id') ,
            'q' => '' ,
            'location' => request()->input('locate') ,
            'radius' => request()->input('distance' , 3000 ) ,
            'page_index' => request()->input('page') ,
            'page_size' => 100  ,
            'coord_type' => 3 ,
            'sortby' => 'distance:1'
        );
        $client = new Client([
            'base_uri' => $url ,
            'timeout' => 10
        ]);
        $res = $client->request('GET', 'geosearch/v3/nearby' , [
            'query' => $data
        ]);
        $body = $res->getBody() ;
        $body = json_decode( $body , true );

        if( data_get( $body , 'status' ) == 0 ) {
            foreach( $body['contents'] as $k => $val ) {
                $body['contents'][ $k ]['location'] = $this->gcjtobd( $val['location'][0] , $val['location'][1] );
            }
        }


        //在这里进行数据转换
        return response()->json( $body ) ;
    }
	
	/**
	 * 详情
	 */
	public function show( $id ) {
		$info = Machine::findOrFail($id) ;
		//更新查看次数
		$user = auth()->guard('api')->user();
		$hasPraise = 0 ;
		if( $user ) {
			//获取是否点赞了
            $hasPraise = MachineCollect::where('machine_id', $id )->where('user_id' , $user->id )->count();
		}
		//Machine::where('id' , $id )->increment('views_count');
		return response()->json([
				'errcode' => 0 ,
				'data' => $info ,
				'has_praise' => $hasPraise
		]);
	}

	public function favor( $id , Request $request ) {
        $info = Machine::findOrFail($id) ;
        //更新查看次数
        $user = auth()->guard('api')->user();
        $collect = $request->input('collect');
        if( $collect ) {
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
        } else {
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
    }

	
	
	/**
	 * 发现吸烟区
	 */
	public function store( Request $request ) {
		\Log::info("添加新娃娃机" , $request->all() );
		$user = auth()->guard('api')->user();
		$data = $request->only([ 'landmark' , 'no' , 'lat' , 'lon' , 'address' , 'cover' ]);
		$data['status'] = 0 ;
		$data['creator_id'] = data_get( $user , 'id' , 0 ) ;
		$point = $this->gcjtobd( data_get( $data , 'lon' , 0 ) , data_get( $data , 'lat' , 0 ) ) ;
		$data['lon'] = data_get( $point , 0 , 0 );
		$data['lat'] = data_get( $point , 1 , 0 );
		$machine = Machine::create( $data );
		if( $machine ) {
			return response()->json([
					'errcode' => 0 ,
					'data' => $machine ,
					'msg' => trans('admin.create_success')
			]);
		}
		return response()->json([
				'errcode' => 10001 ,
				'msg' => trans('admin.create_failed')
		]);
	}

    /**
     * @param $lon
     * @param $lat
     * @return array
     * 上传图片
     */
    public function upload( Request $request ) {
        $config = config('UEditorUpload.upload');
        $upConfig = array(
            "pathFormat" => $config['imagePathFormat'],
            "maxSize" => $config['imageMaxSize'],
            "allowFiles" => $config['imageAllowFiles'],
            'fieldName' => 'file'
        );
        $result = with(new UploadFile($upConfig, $request))->upload();
        if ( 'SUCCESS' == data_get($result, 'state') ) {
            return response()->json([
                'errcode' => 0,
                'data' => data_get($result, 'url'),
                'msg' => '上传完成'
            ]);
        }
        return response()->json([
            'errcode' => 10002,
            'msg' => '保存失败'
        ]);
    }
	
	

	//GCJ-20坐标转换为BD-09坐标
	protected function gcjtobd( $lon , $lat )
	{
		$x_pi = pi() * 3000.0 / 180.0;
		$z = sqrt($lon * $lon + $lat * $lat) + 0.00002 * sin($lat * $x_pi);
		$theta = atan2($lat,$lon) + 0.000003 * cos($lon * $x_pi);
		$bd_lon = $z * cos( $theta ) + 0.0065;
		$bd_lat = $z * sin( $theta ) + 0.006;
		return [ $bd_lon , $bd_lat ];
	}
	
	//BD-09坐标转换为GCJ-20坐标
	protected function bdtogcj( $lon , $lat )
	{
		$x_pi = pi() * 3000.0 / 180.0;
		$x = $lon - 0.0065;
		$y = $lat - 0.006;
		$z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
		$theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
		$gg_lng = $z * cos( $theta );
		$gg_lat = $z * sin( $theta );
		return [ $gg_lng , $gg_lat ];
	}
}