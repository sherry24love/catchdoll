<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string( 'title' , 255 );
            $table->string( 'keyword' , 255 )->nullable();
            $table->string('description' )->nullable();
            $table->integer('category_id' , false , true )->default( 0 );
            $table->string( 'author' , 50 )->nullable();
            $table->string( 'cover' , 255 )->nullable() ;
            $table->text('content');
            $table->smallInteger( 'sort' , false , true )->default( 0 );
            $table->tinyInteger('is_top' , false , true )->default( 0 );
            $table->tinyInteger('is_pic' , false , true )->default( 0 );
            $table->smallInteger('views' , false , true )->default( 0 );
            $table->softDeletes() ;
            $table->timestamps();
        });
        
        
        Schema::create('cms_category', function( Blueprint $table ){
        	$table->increments('id');
        	
        	$table->string('name' , 50 );
        	$table->string('keyword' , 255 );
        	$table->string( 'description' , 255 );
        	$table->smallInteger('parent_id' , false , true )->default( 0 );
        	$table->string('cover' , 255 )->nullable();
        	$table->text('content');
        	$table->smallInteger('sort' , false , true )->default( 0 );
        	//是否推荐
        	$table->tinyInteger('is_recom' , false , true )->default( 0 );
        	
        	$table->timestamps();
        	
        });
        
        Schema::create('cms_adv_target', function( Blueprint $table ){
        	$table->increments('id');
        	$table->string('title');
        	$table->string('slug');
        	$table->string('description' , 255 )->nullable();
        	$table->timestamps() ;
        });
        
        Schema::create('cms_adv', function( Blueprint $table ){
        	
        	$table->increments('id');
        	
        	$table->string('title' , 200 );
        	$table->string('cover' , 255 );
        	$table->string('link' , 255 );
        	
        	$table->timestamp( 'start_at' )->nullable();
        	$table->timestamp( 'end_at')->nullable();
        	
        	$table->smallInteger('sort' , false , true )->default( 0 );
        	$table->tinyInteger('display' , false , true )->default( 1 );
        	
        	$table->timestamps() ;
        	
        });
        
        Schema::create( 'cms_blog',  function( Blueprint $table ){
        	$table->increments('id');
        	
        	$table->integer('user_id' , false , true )->default( 0 );
        	$table->string('title' , 255 );
        	$table->string( 'keyword' , 255 )->nullable();
        	$table->string('description' )->nullable();
        	$table->integer('category_id' , false , true )->default( 0 );
        	$table->string( 'cover' , 255 )->nullable() ;
        	$table->text('content');
        	
        	$table->smallInteger( 'sort' , false , true )->default( 0 );
        	$table->tinyInteger('is_top' , false , true )->default( 0 );
        	$table->tinyInteger('display' , false , true )->default( 1 );
        	
        	$table->smallInteger('views' , false , true )->default( 0 );
        	$table->softDeletes() ;
        	$table->timestamps();
        	
        }) ;
        
        Schema::create( 'cms_feedback',  function( Blueprint $table ){
        	$table->increments('id');
        	$table->integer('user_id' , false , true )->default( 0 );
        	$table->string('mobile' , 20 )->nullable();
        	$table->text('content');
        	$table->tinyInteger('status', false, true )->default ( 0 );
			$table->timestamps ();
		} );
		
		Schema::create ( 'cms_singlepage', function (Blueprint $table) {
			$table->increments ( 'id' );
			$table->string ( 'title', 255 );
			$table->string ( 'author', 50 )->nullable ();
			$table->string ( 'keyword', 255 )->nullable ();
			$table->string ( 'description' )->nullable ();
			$table->string ( 'cover', 255 )->nullable ();
			$table->text ( 'content' );
        	$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cms_posts');
        Schema::dropIfExists('cms_category');
        Schema::dropIfExists('cms_adv_target');
        Schema::dropIfExists('cms_adv');
        Schema::dropIfExists('cms_blog');
        Schema::dropIfExists('cms_feedback');
        Schema::dropIfExists('cms_singlepage');
    }
}
