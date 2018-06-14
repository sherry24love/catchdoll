<?php
namespace Sherrycin\Catchdoll\Controllers ;


use App\Http\Controllers\Controller;

class FeedbackController extends Controller {

	/**
	 * 商品的意见反馈
	 */
	public function feedback() {
		return Admin::content ( function (Content $content) {
	
			$content->header ( '吸烟点反馈管理' );
			$content->description ( '列表' );
	
			$content->body ( $this->feedbackgrid () );
		} );
	}
	protected function feedbackgrid() {
		return Admin::grid ( Feedback::class, function (Grid $grid) {
	
			$user = auth()->guard('admin')->user();
			if (!$user->isParentCompany()){
				$grid->model()->whereIn('pid',function ($query) use ($user){
					return $query-> from('smoking_area')->where('zone_id',$this->getZoneid())->select('id');
				})->with ( 'smokingarea' )->orderBy ( 'id', 'desc' );
			}
	
			$grid->model ()->with ( 'smokingarea' )->orderBy ( 'id', 'desc' );
			$grid->model ()->SmokingArea();
			$grid->id ( 'ID' )->sortable ();
			$grid->column ( 'smokingarea.title', '吸烟点名称' );
			$grid->nickname ( '投诉人' );
			$grid->phone ( '手机号码' );
			$grid->content ( '反馈内容' )->display ( function ($v) {
				return str_limit ( $v, 50 );
			} );
				$grid->read ( '审核状态' )->display ( function ($v) {
					return data_get ( config ( 'global.feedback_status' ), $v, '' );
				} );
	
					$grid->disableRowSelector ();
					$grid->disableExport();
					//$grid->exporter ( new GoodsFeedbackExporter () );
					$grid->disableCreation ();
					$grid->filter ( function (Filter $filter) {
						$filter->disableIdFilter ();
						$filter->like('nickname' , '投诉人');
						$filter->like('phone' , '手机号码');
	
						$filter->where ( function ($query) {
							$input = $this->input;
							return $query->where ( 'pid', function ($query) use ($input) {
								$query->from ( 'smoking_area' )->where ( 'title', 'like', "%{$input}%" )->select ( 'id' );
							} );
						}, '吸烟点名称' );
	
							$filter->where ( function ($query) {
								$input = $this->input;
								return $query->whereIn ( 'pid', function ($query) use ($input) {
									$query->from ( 'smoking_area' )->where ( 'zone_id', $input )->select ( 'id' );
								} );
							}, '行政区名称' )->select ( function () {
								return Region::where ( 'city_id', 237 )->where('area_type' , 4 )->pluck ( 'zone_prefecture', 'id' );
							} );
	
								$filter->where ( function ($query) {
									$input = $this->input;
									return $query->whereIn ( 'pid', function ($query) use ($input) {
										$query->from ( 'smoking_area' )->where ( 'subdistrict_id', $input )->select ( 'id' );
									} );
								}, '街道名称' )->select ( function () {
									return SubDistrict::pluck ( 'name', 'id' );
								} );
	
	
									$filter->between('created_at' , '提交日期')->date();
	
					});
						$grid->actions ( function ( $action) {
							$action->disableDelete ();
							$action->disableEdit ();
							$url = route ( 'admin.smokingarea.feedbackshow', [
									'id' => $action->row->id
							] );
							$action->append ( "<a href='{$url}'><i class='fa fa-globe'></i></a>" );
						} );
		});
	}
	
	public function feedbackshow( $id ) {
		$feedback = Feedback::findOrFail($id);
		$user = auth()->guard('admin')->user();
		$smoking = SmokingArea::where('zone_id',$this->getZoneid())->pluck('id')->toArray();
		if (!$user->isParentCompany() && !in_array($feedback->pid,$smoking)){
			admin_toastr(trans('admin.deny'),'error');
			return back();
		}
		return Admin::content(function (Content $content) use( $id )  {
			$content->header('意见反馈管理');
			$content->description('详情');
			$form = $this->feedbackform()->view( $id );
			if( $form->model()->status > 0 ) {
				$time = $form->model()->deal_time ;
				$form->display('deal_time')->with( function( $v ) use( $time ) {
					return $time  ? date('Y-m-d' , $time ) : '' ;
				} );
			}
			$form->disableSubmit();
			$form->disableReset();
			$content->row( $form );
			if( $form->model()->read == 0 ) {
				$url = route('admin.smokingarea.feedback.setread' , ['id' => $form->model()->id ] );
				$pannel = "<div class='col-sm-off-2 col-sm-8'><a href='{$url}' class='btn btn-primary'>设置为已读</a></div>";
				$content->row( $pannel );
			}
		});
	}
	
	protected function feedbackform() {
		return Admin::form( Feedback::class, function (Form $form) {
			// $form->display('store.company' , '商家名称') ;
			$form->display ( 'nickname', '反馈人' );
			$form->display ( 'phone', '联系电话' );
			$form->display ( 'content', '投诉内容' );
			$form->display ( 'cover', '图片' )->with ( function ($v) {
				return "<img src='" . config('global.file_domain') . $v . "' width='100%' />" ;
			} );
				$form->display('read' , '处理状态')->with( function( $v ){
					return data_get( config('global.feedback_status') , $v ) ;
				} );
					$form->display('create_time' , '投诉时间')->with( function( $v ){
						return $v ? date('Y-m-d' , $v ) : $this->created_at ;
					} );
		}) ;
	}
	
	public function setRead( $id ) {
		$feedback = Feedback::findOrFail( (int) $id );
		$feedback->read = 1 ;
		if( $feedback->save() ) {
			//event(new \App\Events\userActionEvent('\App\Modules\Admin\Http\Feedback', $feedback->id, 'feedback.agree', '将编号为' . $feedback->id . '的建议反馈设置为采纳' ));
			admin_toastr('处理完成');
			return back();
		}
		admin_toastr('处理失败');
		return back();
	}
}