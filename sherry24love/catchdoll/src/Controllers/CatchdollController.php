<?php
namespace Sherrycin\Catchdoll\Controllers ;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Grid\Displayers\Actions;

//use QrCode;
use GuzzleHttp\Client;
use Sherrycin\Catchdoll\Models\Machine  ;
use Encore\Admin\Grid\Filter;

use App\Http\Controllers\Controller;

class CatchdollController extends Controller {
	use ModelForm;


	public function index() {
		return Admin::content(function (Content $content) {

			$content->header( trans('catchdoll.machine') ) ;
			$content->description( trans('admin.list' ) );

			$content->body($this->grid());
		});
	}


	protected function grid() {
		return Admin::grid( Machine::class, function (Grid $grid) {
			$user = auth()->guard('admin')->user();
			$grid->model()->orderBy('id' , 'desc');
			$grid->id('ID')->sortable();
			$grid->landmark('地标名称');
			//$grid->no( trans('catchdoll.no') )->limit( 30 );
			$grid->merchant('商家名称')->limit( 30 );
			
			/**
			$grid->column ( 'subdistrict.name', '街道' )->limit ( 30 )->display ( function ($v) {
				return $v ? $v : '';
			} );
			**/
			$grid->cover ( trans('catchdoll.cover' ) )->image ();
			//$grid->lat ( trans('catchdoll.lat')  );
			//$grid->lon ( trans('catchdoll.lon' ) );
			$grid->address( trans('catchdoll.address') ) ;
			$states = [ 
					'on' => [ 
							'value' => 1,
							'text' => trans('admin.yes') ,
							'color' => 'primary' 
					],
					'off' => [ 
							'value' => 0,
							'text' => trans('admin.no' ) ,
							'color' => 'default' 
					] 
			];
			/**
			$grid->monopoly_no ( '二维码' )->display ( function ($v) {
				$url = config ( 'global.wap_domain' ) . "/#/smoke/" . $this->id;
				return "<img src='data:image/png;base64," . base64_encode ( QrCode::format ( 'png' )->size ( 100 )->margin ( 1 )->generate ( $url ) ) . "' style='max-width:200px;max-height:200px;' class='img img-thumbnail' />";
			} );
			**/
			$grid->score( trans('catchdoll.score') ) ;
			$grid->sort( trans('catchdoll.sort') )->sortable()->editable();
			$grid->top ( trans('catchdoll.if_top') )->switch ( $states );
			$grid->status ( trans('catchdoll.if_auth')  )->display ( function ($v) {
				return data_get ( config ( 'catchdoll.status' ), $v, '' );
			} );
			$grid->created_at ( trans('catchdoll.created_at') );
			$grid->disableExport();
			
			$grid->actions ( function (Actions $action) {
				if ($action->row->status == 0) {
					$link = route ( 'catchdoll.catchdoll.agree', [ 
							'id' => $action->row->id 
					] );
					$button = sprintf ( "<a href='%s' class='btn %s'>%s</a>", $link, "btn-smoking-auth-agree", '通过审核' );
					$action->append ( $button );
					
					$link = route ( 'catchdoll.catchdoll.disagree', [ 
							'id' => $action->row->id 
					] );
					$button = sprintf ( "<a href='%s' class='btn %s'>%s</a>", $link, "btn-smoking-auth-disagree", '不通过审核' );
					$action->append ( $button );
				}
			} );
			
			$grid->disableRowSelector ();
			$grid->filter ( function (Filter $filter) {
				$filter->disableIdFilter ();
				$filter->like ( 'no', trans('catchdoll.no') );
				$filter->like ( 'address', trans('catchdoll.address' ) );
			} );
		} );
	}


	public function create() {
		return Admin::content(function (Content $content) {

			$content->header( trans('catchdoll.machine') ) ;
			$content->description( trans('catchdoll.create') ) ;

			$content->body($this->form());
		});
	}

	public function store() {
		$form = $this->form();
		//自己创建的自己变成审核
		$form->saving( function( $form ) {
			$user = auth()->guard('admin')->user();
			$form->model()->status = 1 ;
			$form->model()->creator_id = $user->id ;
			$form->model()->auth_id = $user->id ;
		});

		return $form->store();
	}


	public function edit( $id ) {
		return Admin::content(function (Content $content) use ($id) {

			$content->header( trans('catchdoll.machine') ) ;
			$content->description( trans('admin.edit' ) ) ;

			$content->body($this->form()->edit($id));
		});
	}

	public function update( $id ) {
		$form = $this->form();
		return $form->update( $id );
	}

	protected function form() {
		return Admin::form( Machine::class, function (Form $form) {
		    $form->text('landmark' , trans('catchdoll.landmark') )->rules('required' , [
		        'required' => trans('catchdoll.need_landmark')
            ]);
		    $form->text('merchant' , '商家名称')->rules('required' , [
                'required' => trans('catchdoll.need_merchant')
            ]);
		    /**
			$form->text('no' , trans('catchdoll.no') )->rules('required' , [
					'required' => trans('catchdoll.need_no')
			]);
             **/
			$form->image ( 'cover', trans('catchdoll.cover') );
			$form->bmap ( 'lat', 'lon', trans('catchdoll.map') )->rules ( 'required', [ 
					'required' => trans('catchdoll.require_map') 
			] );
			
			$form->text ( 'address' , trans('catchdoll.address') )->rules ( 'required:cover|max:255', [ 
					'required' => trans('catchdoll.require_address'),
					'max' => '地址最长为%s个字符' 
			] );
			$states = [ 
					'on' => [ 
							'value' => 1,
							'text' => trans('admin.yes') ,
							'color' => 'success' 
					],
					'off' => [ 
							'value' => 0,
							'text' => trans('admin.no') ,
							'color' => 'danger' 
					] 
			];
			$form->switch ( 'status', trans('catchdoll.if_auth') )->states ( $states );
			$form->number('sort' , trans('catchdoll.sort') )->default( 0 );
			$form->switch('top' , trans('catchdoll.if_top' ) )->states( $states )->default( 0 ) ;
			$form->number ( 'score', trans('catchdoll.score') )->default ( 0 );
			
			$form->saved ( function ($form) {
				dispatch ( new \Sherrycin\Catchdoll\Jobs\RsyncMachines( $form->model () ) );
			} );
		});
	}

	public function destroy( $id ) {
		$machine = Machine::findOrFail( $id );
		if( $machine->lbs_id ) {
			dispatch( new \Sherrycin\Catchdoll\Jobs\RsyncMachines( $machine , 'del' ) ) ;
		}
		if ($this->form()->destroy($id)) {
			return response()->json([
					'status'  => true,
					'message' => trans('admin.delete_succeeded'),
			]);
		} else {
			return response()->json([
					'status'  => false,
					'message' => trans('admin.delete_failed'),
			]);
		}
	}

	/**
	 * 审核通过
	 * @param unknown $id
	 */
	public function agree( $id ) {

		$user = auth()->guard('admin')->user();
		$row = Machine::where('id' , $id )->update([
				'status' => 1 ,
				'auth_id' => $user->id ,
		]);
		if( $row ) {
			$area = Machine::findOrFail( $id ) ;
			dispatch ( new \Sherrycin\Catchdoll\Jobs\RsyncMachines( $area ) );
			admin_toastr ( trans('catchdoll.auth_ok' ) );
			return back();
		} else {
			admin_toastr( trans('catchdoll.auth_failed') , 'error' );
			return back();
		}
	}

	/**
	 * 审核不通过
	 * @param unknown $id
	 */
	public function disagree( $id) {
		$user = auth ()->guard ( 'admin' )->user ();
		$row = Machine::where ( 'id', $id )->update ( [
				'auth_status' => 2,
				'auth_id' => $user->id,
		] );
		if ($row) {
			$area = Machine::findOrFail ( $id );
			dispatch ( new \Sherrycin\Catchdoll\Jobs\RsyncMachines( $area ) );
			admin_toastr ( trans('catchdoll.auth_ok' ) );
			return back ();
		} else {
			admin_toastr ( trans('catchdoll.auth_failed') , 'error' );
			return back ();
		}
	}
}