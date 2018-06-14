<?php
namespace Sherrycin\Cms\Controllers;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

use Sherrycin\Cms\Models\Advertisement as Adv ;
use Sherrycin\Cms\Models\Advtarget ;

use Encore\Admin\Controllers\ModelForm ;

class AdvertisementController extends BaseController {
	use ModelForm;
	
	public function index() {
		return Admin::content(function (Content $content) {
			$content->header(trans('cms.advertisement'));
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
		return Admin::grid( Adv::class, function (Grid $grid) {
			$grid->id('ID')->sortable();

			$grid->model()->orderBy('id' , 'desc');
			$grid->title(trans('cms.title'));
			$grid->column('advtarget.title' , trans('cms.advtarget'))->sortable();
			$grid->cover('封面')->image();
			$grid->start_at(trans('cms.start_at'));
			$grid->end_at(trans('cms.end_at'));
			$grid->sort( trans('cms.sort') )->sortable()->editable();
			$states = [
					'on'  => ['value' => 1, 'text' => trans('cms.yes') , 'color' => 'success'],
					'off' => ['value' => 0, 'text' => trans('cms.no') , 'color' => 'danger'],
			];
			$grid->display( trans('cms.display' ) )->switch($states);
			//$grid->created_at(trans('admin.created_at'));
			//$grid->updated_at(trans('admin.updated_at'));
			$grid->filter(function ($filter) {
				$filter->disableIdFilter();
				$filter->like('title', trans('cms.title'));
				
				$filter->is('target_id' , '广告位')->select(
					Advtarget::pluck('title' , 'id' )			
				);
				
			});
			$grid->disableExport();
		});
	}
	
	/**
	 * 新增
	 */
	public function create() {
		return Admin::content(function (Content $content) {
			$content->header(trans('cms.advertisement'));
			$content->description(trans('admin.create'));
			$content->body($this->form());
		});
	}
	
	/**
	 * 修改
	 */
	public function edit( $id ) {
		return Admin::content(function (Content $content) use( $id ) {
			$content->header(trans('cms.advertisement'));
			$content->description(trans('admin.edit'));
			$content->body($this->form()->edit( $id ) );
		});
	}
	
	
	protected function form() {
		return Admin::form( Adv::class, function ( Form $form) {
			$form->display('id', 'ID');
		
			$form->text('title', trans('cms.title'))->rules('required');
			$form->select('target_id', trans('cms.advtarget'))->options(function() {
				return Advtarget::pluck('title' , 'id');
			})->rules('required');
			$form->text('link' , trans('cms.link'))->help( $this->linkHelper() )->rules('required');
			$form->image('cover', trans('cms.cover'));
			$form->dateRange('start_at', 'end_at' , trans('cms.adv_display_date') );
			$form->number('sort', trans('cms.sort'))->default( 0 )->help('序号不能为负数，且序号越大越靠前');
			$states = [
					'on'  => ['value' => 1, 'text' => trans('cms.yes') , 'color' => 'success'],
					'off' => ['value' => 0, 'text' => trans('cms.no') , 'color' => 'danger'],
			];
			$form->switch( 'display' , trans('cms.display' ) )->states($states);
			$form->display('created_at', trans('admin.created_at'));
			$form->display('updated_at', trans('admin.updated_at'));
		});
	}
	
	
	protected function linkHelper() {
		return '广告地址规则';
	}
}
