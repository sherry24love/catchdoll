<?php
namespace Sherrycin\Cms\Controllers;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

use Sherrycin\Cms\Models\Single ;

use Encore\Admin\Controllers\ModelForm ;

class SingleController extends BaseController {
	use ModelForm;
	
	public function index() {
		
		return Admin::content(function (Content $content) {
			$content->header( '系统页面' );
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
		return Admin::grid( Single::class, function (Grid $grid) {
			$user = auth()->guard('admin')->user();
			$grid->id('ID')->sortable();
			$grid->model()->orderBy('id' , 'desc' );
			$grid->title(trans('cms.title'));
			$grid->keyword(trans('cms.keyword'));
	
			$grid->created_at(trans('admin.created_at'));
			$grid->updated_at(trans('admin.updated_at'));
			$grid->actions( function( $action ){
				$action->disableDelete();
			});
			$grid->disableRowSelector();
			$grid->filter(function ($filter) {
				$filter->disableIdFilter();
				$filter->like('title', trans('cms.title'));
			});
			$grid->disableExport();
		});
	}
	
	/**
	 * 新增
	 */
	public function create() {
		return Admin::content(function (Content $content) {
			$content->header(trans('cms.singlepage'));
			$content->description(trans('admin.create'));
			$content->body($this->form());
		});
	}
	
	/**
	 * 修改单页面的内容
	 */
	public function edit( $id ) {
		return Admin::content(function (Content $content) use( $id ) {
			$content->header(trans('cms.singlepage'));
			$content->description(trans('admin.edit'));
			$content->body($this->form()->edit( $id ) );
		});
	}
	
	
	protected function form() {
		return Admin::form( Single::class, function ( Form $form) {
			//$form->display('id', 'ID');
			$orgId = $this->orgId();
			$form->text('title', trans('cms.title'))->rules('required');
			$form->text('author', trans('cms.author'));
			$form->text('keyword', trans('cms.keyword'));
			$form->text('description', trans('cms.description'));
			$form->image('cover', trans('cms.cover'));
			$form->ueditor('content', trans('cms.content'))->rules('required');
		
			$form->display('created_at', trans('admin.created_at'));
			$form->display('updated_at', trans('admin.updated_at'));
		});
	}
}
