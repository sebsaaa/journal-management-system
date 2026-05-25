<?php namespace Seba\Punktoza\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateJournalsTable Migration
 *
 * @link https://docs.octobercms.com/4.x/extend/database/structure.html
 */
return new class extends Migration
{
    /**
     * up builds the migration
     */
    public function up()
    {
        Schema::create('seba_punktoza_journals', function(Blueprint $table) {
            $table->id();
            $table->string('uid')->nullable();
            $table->text('title')->nullable();
            $table->string('issn')->nullable();
            $table->string('eissn')->nullable();
            $table->integer('points')->default(0);
            $table->text('disciplines')->nullable();
            $table->timestamps();
        });
    }

    /**
     * down reverses the migration
     */
    public function down()
    {
        Schema::dropIfExists('seba_punktoza_journals');
    }
};
