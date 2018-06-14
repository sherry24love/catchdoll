<?php 

namespace Sherrycin\Cms\Controllers ;


use Illuminate\Routing\Controller;

class BaseController extends Controller {
	
	protected $orgId = 0 ;
	
	public function orgId() {
		$this->orgId = auth()->guard('admin')->User()->org_id ;
		if( !$this->orgId ) {
			//如果没有Org id 则尝试从 url 或者 session中获取
			$this->orgId = request()->get('org_id' , 0 );
			if( $this->orgId ) {
				session( ['org_id' => $this->orgId ]) ;
			}
		}
		if( !$this->orgId ) {
			//如果没有Org id 则尝试从 url 或者 session中获取
			$this->orgId = session('org_id');
		}
		return $this->orgId ;
	}
}