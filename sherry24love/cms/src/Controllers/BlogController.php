<?php
namespace Sherrycin\Cms\Controllers;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

use Sherry\Cms\Models\Blog ;

use Encore\Admin\Controllers\ModelForm ;
use App\Admin\Extensions\Tools\Trashed;
use App\User;

class BlogController extends BaseController {
	use ModelForm;
	
	public function index() {
		
		return Admin::content(function (Content $content) {
			$content->header(trans('cms.blog'));
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
		return Admin::grid( Blog::class, function (Grid $grid) {
			$grid->model()->orderBy('id' , 'desc' );
			if (request('trash') == 1) {
				$grid->model()->onlyTrashed();
			}
			$grid->id('ID')->sortable();
			$grid->title(trans('cms.title'));
			$grid->column('doctor.name' , '医生姓名');
			$grid->content( '简介' )->display( function( $v ){
				return str_limit( $v , 50 );
			} );
			$grid->display('是否显示')->display( function( $v ){
				return $v == 1 ? '显示' : '不显示' ;
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
			$content->header(trans('cms.blog'));
			$content->description(trans('admin.create'));
			$content->body($this->form());
		});
	}
	
	/**
	 * 修改单页面的内容
	 */
	public function edit( $id ) {
		return Admin::content(function (Content $content) use( $id ) {
			$content->header(trans('cms.blog'));
			$content->description(trans('admin.edit'));
			$content->body($this->form()->edit( $id ) );
		});
	}
	
	
	protected function form() {
		return Admin::form( Blog::class, function ( Form $form) {
			$form->text('user.mobile', '医生手机号' )->rules('required');
			$form->text('title', trans('cms.title'))->rules('required');
			$form->text('keyword', trans('cms.keyword'));
			$form->text('description', trans('cms.description'));
			$form->textarea('content', trans('cms.content'))->rules('required');
			$form->multipleImage('images', trans('cms.cover'));
			$states = [
					'on'  => ['value' => 1, 'text' => trans('cms.yes') , 'color' => 'success'],
					'off' => ['value' => 0, 'text' => trans('cms.no') , 'color' => 'danger'],
			];
			$form->switch('display' , '是否审核')->states( $states );
			$form->display('created_at', trans('admin.created_at'));
			$form->display('updated_at', trans('admin.updated_at'));
			$form->ignore('user.mobile');
			$form->saving( function( Form $form ){
				$mobile = request('user.mobile');
				//获取 这个手机号
				$user = User::where('mobile' , $mobile )->first();
				if( empty( $user ) ) {
					admin_toastr('医生不存在' , 'error');
					return back()->withInput();
				}
				$form->model()->uid = $user->id ;
			});
		});
	}
	

	/**
	 * 从回收站中移出
	 * @param unknown $id
	 */
	public function restore( $id ) {
		$product = Blog::onlyTrashed()->find( $id );
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
