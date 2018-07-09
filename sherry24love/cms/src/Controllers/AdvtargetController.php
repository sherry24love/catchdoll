<?php
namespace Sherrycin\Cms\Controllers;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\ModelForm ;


use Sherrycin\Cms\Models\Advtarget ;
use Sherrycin\Cms\Models\Advertisement;



class AdvtargetController extends BaseController {
	use ModelForm;
	
	public function index() {
		
		return Admin::content(function (Content $content) {
			$content->header(trans('cms.advtarget'));
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
		return Admin::grid( Advtarget::class, function (Grid $grid) {
			$grid->id('ID')->sortable();
			$grid->model()->orderBy('id' , 'desc');
			$grid->title(trans('cms.adv_target_title'));
			$grid->slug(trans('cms.slug'));
	
			$grid->created_at(trans('admin.created_at'));
			$grid->updated_at(trans('admin.updated_at'));
			$grid->filter(function ($filter) {
				$filter->disableIdFilter();
				$filter->like('title', trans('cms.adv_target_title'));
				$filter->like('slug' , '别名');
			});
			$grid->disableExport();
		});
	}
	
	/**
	 * 新增
	 */
	public function create() {
		return Admin::content(function (Content $content) {
			$content->header(trans('cms.advtarget'));
			$content->description(trans('admin.create'));
			$content->body($this->form());
		});
	}
	
	/**
	 * 修改单页面的内容
	 */
	public function edit( $id ) {
		return Admin::content(function (Content $content) use( $id ) {
			$content->header(trans('cms.advtarget'));
			$content->description(trans('admin.edit'));
			$content->body($this->form()->edit( $id ) );
		});
	}
	
	
	protected function form() {
		return Admin::form( Advtarget::class, function ( Form $form) {
			$form->display('id', 'ID');
		
			$form->text('title', trans('cms.title'))->rules('required');
			$form->text('slug', trans('cms.slug'))->rules('required');
			$form->textarea('description', trans('cms.description'))->placeholder( trans('cms.advtarget_description') );
			$form->display('created_at', trans('admin.created_at'));
			$form->display('updated_at', trans('admin.updated_at'));
		});
	}
	
	
	public function destroy($id) {
		$count = Advertisement::where('target_id' , $id )->count();
		if( $count ) {
			return response()->json([
					'status'  => false,
					'message' => '请先删除当前广告位下的广告信息',
			]);
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
}
