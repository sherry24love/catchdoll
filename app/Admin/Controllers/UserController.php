<?php

namespace App\Admin\Controllers;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

use App\User ;

class UserController extends Controller
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

            $content->header( '会员' ) ;
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

            $content->header('header');
            $content->description('description');

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

            $content->header( '会员' ) ;
            $content->description('description');

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
        return Admin::grid( User::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->username('用户名');
			$grid->mobile('手机号码') ;
			$grid->nickname('昵称');
			$grid->avatar('头像')->image();
			$grid->level('等级')->select( config('global.user_level' ) );
			$grid->disableExport();
			$grid->disableCreateButton() ;
            $grid->created_at('创建时间');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form( User::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->display('mobile' , '手机号码');
            $form->display('nickname' , '昵称') ;
            $form->display('avatar' , '用户头像')->with( function( $v ){
                return "<img src='{$v}' />" ;
            } );

            $form->select('level' , '用户级别')->options( config('global.user_level')) ;
        });
    }
}
