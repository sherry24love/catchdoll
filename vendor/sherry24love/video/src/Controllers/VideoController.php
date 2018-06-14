<?php

namespace Sherrycin\Video\Controllers;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

use Sherrycin\Video\Models\Video ;

class VideoController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header( trans( 'video.video' ) ) ;
            $content->description( trans( 'admin.list' ) ) ;

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header( trans( 'video.video' ) ) ;
            $content->description(trans('admin.edit') );

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header( trans( 'video.video' ) ) ;
            $content->description(trans('admin.create') );

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid( Video::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->title( trans('video.title' ));
            $grid->desc( trans('video.desc' ) );
			$grid->url( trans('video.video'))->display( function( $v ){
                return "<video src='http://{$v}' style='width: 200px;'></video>" ;
            });
			$grid->size( trans('video.size' ));
			$grid->column('user.nickname' , trans('video.nickname' ));
			$grid->status( trans('video.status' ))->display( function( $v ){
				return $v ? '已审核' : '未审核' ;
			} );
			$grid->disableExport();
            $grid->created_at( trans('video.created_at') );
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form( Video::class, function (Form $form) {

            //$form->display('id', 'ID');
            $form->text('title' , trans('video.title') ) ;
            $form->text('desc' , trans('video.desc')) ;
            //这里不可以修改
            $form->select('user_id' , '发布用户')->options(function(){
                return \App\User::pluck('username' , 'id' );
            });
            //这里暂时不能上传
            $form->file('url' , trans('video.video'))->help("暂时不可以上传大文件");
            $form->switch('status' , trans('video.status'));
            $form->switch('top' , trans('video.top'));
            $form->number('sort' , '排序');
        });
    }
}
