<?php
namespace Sherrycin\Cms\Controllers;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

use Sherry\Cms\Models\Notice ;

use Encore\Admin\Controllers\ModelForm ;
use App\Admin\Extensions\Tools\Trashed;

class NoticeController extends BaseController {
	use ModelForm;
	
	public function index() {
		
		return Admin::content(function (Content $content) {
			$content->header(trans('cms.notice'));
			$content->description(trans('admin.list'));
			$content->body($this->grid()->render());
		});
	}
	
	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function grid()
	{
		return Admin::grid( Notice::class, function (Grid $grid) {
			
			$grid->model()->orderBy('id' , 'desc' );
			if (request('trash') == 1) {
				$grid->model()->onlyTrashed();
			}
			$grid->id('ID')->sortable();
			$grid->title(trans('cms.title'));
			$grid->content( '通知内容' )->display( function( $v ){
				return str_limit( $v , 50 );
			} );
	
			$grid->created_at(trans('admin.created_at'));
			$grid->updated_at(trans('admin.updated_at'));
			$grid->disableExport();
			$grid->disableRowSelector();
			$grid->disableBatchDeletion();
			$grid->filter(function ($filter) {
				$filter->disableIdFilter();
				$filter->like('title', trans('cms.title'));
			});
			
			$grid->tools( function( $tools ){
				$tools->append( new Trashed() );
			});
			$grid->actions( function( $action ) {
				if (request('trash') == 1) {
					$action->disableEdit();
				}
			});
		});
	}
	
	/**
	 * 新增
	 */
	public function create() {
		return Admin::content(function (Content $content) {
			$content->header(trans('cms.notice'));
			$content->description(trans('admin.create'));
			$content->body($this->form());
		});
	}
	
	/**
	 * 修改单页面的内容
	 */
	public function edit( $id ) {
		return Admin::content(function (Content $content) use( $id ) {
			$content->header(trans('cms.notice'));
			$content->description(trans('admin.edit'));
			$content->body($this->form()->edit( $id ) );
		});
	}
	
	
	protected function form() {
		return Admin::form( Notice::class, function ( Form $form) {
			$form->display('id', 'ID');
			$form->text('title', trans('cms.title'))->rules('required');
			$form->textarea('content', trans('cms.content'))->rules('required');
			$form->display('created_at', trans('admin.created_at'));
			$form->display('updated_at', trans('admin.updated_at'));
		});
	}
	
	/**
	 * 从回收站中移出
	 * @param unknown $id
	 */
	public function restore( $id ) {
		$product = Notice::onlyTrashed()->find( $id );
		if( $product->restore() ) {
			return response()->json([
					'status'  => true ,
					'message' => '恢复完成',
			]);
		} else {
			return response()->json([
					'status'  => false,
					'message' => '恢复失败',
			]);
		}
	}
}
