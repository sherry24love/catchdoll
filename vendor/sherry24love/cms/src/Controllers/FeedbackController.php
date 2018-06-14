<?php
/**
 * 2017年5月5日
 * author email 349017188@qq.com
 * author qq 349017188
 * edit by sherry
 * 评价管理
 */

namespace Sherrycin\Cms\Controllers ;


use Illuminate\Routing\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Form ;
use Encore\Admin\Layout\Content;

use Encore\Admin\Controllers\ModelForm ;
use Sherrycin\Cms\Models\Feedback ;
use Encore\Admin\Grid\Filter;

class FeedbackController extends Controller {
	use ModelForm ;
	
	
	public function index() {
		
		return Admin::content( function( Content $content ){
			$content->header('用户反馈');
			$content->description('列表');
			$content->body( $this->grid()->render() );
		});
	}
	
	protected function grid() {
		return Admin::grid( Feedback::class , function( Grid $grid ){
			$grid->model()->with( 'user' );
			$grid->id ( 'ID' )->sortable ();
			$grid->model()->orderBy('id' , 'desc' );
			
			$grid->column('user.mobile' , '用户');
			
			$grid->content('评论内容');
			$state = [
					'on'  => ['value' => 1, 'text' => '是' , 'color' => 'success'],
					'off' => ['value' => 0, 'text' => '否' , 'color' => 'danger'],
			] ;
			$grid->status('是否处理')->switch( $state );
			$grid->created_at('创建时间');
			$grid->updated_at('最后更新');
			$grid->disableCreation() ;
			$grid->disableExport() ;
			$grid->disableRowSelector() ;
			$grid->disableActions() ;
			$grid->filter( function( Filter $filter ) {
				$filter->disableIdFilter();
				$filter->like('content' , '关键词');
				$filter->is('status' , '是否处理' )->select([
						0 => '未处理' ,
						1 => '已处理' ,
				]);
			}  );
		});
	}
	
	protected function form() {
		return Admin::form( Feedback::class , function( Form $form ) {
			$form->hidden('status' );
		});
	}
	
}