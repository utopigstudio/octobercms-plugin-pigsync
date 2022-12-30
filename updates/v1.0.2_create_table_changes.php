<?php namespace Utopigs\Pigsync\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateTableChanges extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('utopigs_pigsync_changes');
        Schema::enableForeignKeyConstraints();

        Schema::create('utopigs_pigsync_changes', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('basename');
            $table->string('filename');
            $table->text('payload');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('commit_id')->unsigned()->nullable();
            $table->string('checksum');

            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('user_id')->references('id')->on('backend_users')->onDelete('set null');
            $table->foreign('commit_id')->references('id')->on('utopigs_pigsync_commits')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('utopigs_pigsync_changes');
    }
}
