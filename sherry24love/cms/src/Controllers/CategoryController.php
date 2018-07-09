<?php
namespace Sherrycin\Cms\Controllers;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Tree;
use Encore\Admin\Controllers\ModelForm ;

use Sherrycin\Cms\Models\Category ;
use Sherrycin\Cms\Models\Posts;

class CategoryController extends BaseController
{
	use ModelForm;
	
	public function index() {
		
		return Admin::content(function(Content $content ){
			$content->header(trans('cms.category'));
			$content->description(trans('admin.list'));
			$content->row( $this->treeView()->render() );
		});
	}
	
	public function create() {
		return Admin::content(function (Content $content) {
			$content->header(trans('cms.category'));
			$content->description(trans('admin.create'));
			$content->row($this->form());
		});
	}
	
	/**
	 * Edit interface.
	 *
	 * @param string $id
	 *
	 * @return Content
	 */
	public function edit($id) {
		return Admin::content(function (Content $content) use ($id) {
			$content->header(trans('cms.category'));
			$content->description(trans('admin.edit'));
	
			$content->row($this->form()->edit($id));
		});
	}
	
	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	public function form() {
		return Category::form(function (Form $form) {
			$user = auth()->guard('admin')->user();
			$orgId = $user->org_id ;
			$form->display('id', 'ID');
			$form->select('parent_id', trans('admin.parent_id'))->options( function( ) use( $orgId ) {
				$cate = new Category();
				return $cate->selectOwnTree( $orgId );
			});
			$form->text('name', trans('cms.category'))->rules('required');
			$form->text('keyword', trans('cms.keyword'));
			$form->text('description', trans('cms.description'));
			$form->image('cover', trans('cms.cover'))->rules('required')->help('请上传图片');
			$form->ueditor('content' , trans('cms.content') );
			$form->display('created_at', trans('admin.created_at'));
			$form->display('updated_at', trans('admin.updated_at'));
		});
	}
	
	/**
	 * @return \Encore\Admin\Tree
	 */
	protected function treeView()
	{
		return Category::tree(function (Tree $tree) {
			$user = auth()->guard('admin')->user();
			$tree->branch(function ($branch) {
				$payload = "<strong>{$branch['name']}</strong>";
				return $payload;
			});
		});
	}
	
	/**
	 * Redirect to edit page.
	 *
	 * @param int $id
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function show($id)
	{
		return redirect()->action(
				'\Sherry\cms\Controllers\CategoryController@edit', ['id' => $id]
				);
	}
	
	public function destroy($id)
	{
		$count = Category::where('parent_id' , $id )->count();
		if( $count ) {
			return response()->json([
					'status'  => false,
					'message' => '请先删除下级分类',
			]);
		}
		$count = Posts::where('category_id' , $id )->count();
		if( $count ) {
			return response()->json([
					'status'  => false,
					'message' => '请先删除当前分类的资讯信息',
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
