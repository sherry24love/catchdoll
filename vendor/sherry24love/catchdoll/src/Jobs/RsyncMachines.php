<?php
namespace Sherrycin\Catchdoll\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log ;
use GuzzleHttp\Client;
use Sherrycin\Catchdoll\Models\Machine;

class RsyncMachines implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $area ;
	protected $cordType = 1 ;
	protected $op = 'update' ;
	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct( Machine $area , $op = 'update' )
	{
		$this->area = $area ;
		//
		$this->op = $op ;
	}

	
	public function handle() {
		date_default_timezone_set('Asia/Shanghai') ;
		$fileName = $this->area->cover ;
		$filePath = storage_path('app/admin/' .  $fileName );
		if( $this->op != 'update' ) {
			$this->deletePoi( $this->area );
			return true ;
		}
		if( $this->area->lat && $this->area->lon ) {
			//如果有经纬度则不需要 去解析  
			$this->cordType = 3 ;
		} else {
			$this->fail("{$this->area->id}没有经纬度");
		}
		if( $this->area->lbs_id ) {
			//更新
			$this->updatePoi( $this->area , $this->area->lat , $this->area->lon ) ;
		} else {
			$out = $this->createPoi( $this->area , $this->area->lat , $this->area->lon ) ;
			$this->area->lbs_id = data_get( $out , 'id' );
		}
		$this->area->save();
			
		//Log::info( 'update_record:' . $row );
	}
	
	

	protected function geocoder( $lat , $lon ) {
		$api = "http://api.map.baidu.com";
		$client = new Client ( [
				'base_uri' => $api ,
				'timeout' => 10
		]);
		$params = [
				'ak' => config('catchdoll.map_server_ak') ,
				'output' => 'json' ,
				'location' => $lat . ',' . $lon ,
				'coordtype' => 'wgs84ll'
		];
		$res = $client->request('GET', 'geocoder/v2/' , [
				'query' => $params
		]);
		$body = $res->getBody() ;
		$body = json_decode( $body , true );
		return $body ;
	}
	
	protected function createPoi( $area , $lat , $lon ) {
		$api = "http://api.map.baidu.com";
		$client = new Client ( [
				'base_uri' => $api ,
				'timeout' => 10
	
		]);
		$params = [
				'ak' => config('catchdoll.map_server_ak') ,
				'output' => 'json' ,
				'coord_type' => $this->cordType ,
				'title' => $area->title ,
				'latitude' => $lat ,
				'longitude' => $lon ,
				'geotable_id' => config('catchdoll.lbs_table_id') ,
				'cover' => $area->cover ,
				'smoking_area_id' => $area->id ,
				'address' => $area->address ,
				'display' => $area->display ,
				'auth' => $area->auth_status ,
				'no' => $area->no ,
				'unit_property' => $area->unit_property
		];
		$res = $client->request('POST', 'geodata/v3/poi/create' , [
				'form_params' => $params
		]);
		$body = $res->getBody() ;
		$body = json_decode( $body , true );
		return $body ;
	}
	
	protected function updatePoi( $area , $lat , $lon ) {
		$api = "http://api.map.baidu.com";
		$client = new Client ( [
				'base_uri' => $api ,
				'timeout' => 10
	
		]);
		$params = [
				'id' => $area->lbs_id ,
				'ak' => config('catchdoll.map_server_ak') ,
				'output' => 'json' ,
				'coord_type' => $this->cordType ,
				'title' => $area->title ,
				'latitude' => $lat ,
				'longitude' => $lon ,
				'geotable_id' => config('catchdoll.lbs_table_id') ,
				'cover' => $area->cover ,
				'smoking_area_id' => $area->id ,
				'address' => $area->address ,
				'display' => $area->display ,
				'auth' => $area->auth_status ,
				'no' => $area->no ,
				'unit_property' => $area->unit_property
		];
		$res = $client->request('POST', 'geodata/v3/poi/update' , [
				'form_params' => $params
		]);
		$body = $res->getBody() ;
		$body = json_decode( $body , true );
		return $body ;
	}
	
	
	protected function deletePoi( $area ) {
		$api = "http://api.map.baidu.com";
		$client = new Client ( [
				'base_uri' => $api ,
				'timeout' => 10
	
		]);
		$params = [
				'id' => $area->lbs_id ,
				'ak' => config('catchdoll.map_server_ak') ,
				'output' => 'json' ,
				'geotable_id' => config('catchdoll.lbs_table_id') ,
				'address' => $area->address 
		];
		$res = $client->request('POST', 'geodata/v3/poi/delete' , [
				'form_params' => $params
		]);
		$body = $res->getBody() ;
		$body = json_decode( $body , true );
		return $body ;
	}
	
}