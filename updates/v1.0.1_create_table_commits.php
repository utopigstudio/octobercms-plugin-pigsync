<?php namespace Utopigs\Pigsync\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateTableCommits extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('utopigs_pigsync_commits');
        Schema::enableForeignKeyConstraints();

        Schema::create('utopigs_pigsync_commits', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('processed')->default(0);
            $table->string('message');
            $table->string('author_name');
            $table->string('author_email');

            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('utopigs_pigsync_commits');
    }
}
