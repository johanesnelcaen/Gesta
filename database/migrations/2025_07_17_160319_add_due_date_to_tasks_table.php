<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::table('tasks', function (Blueprint $table) {

        if (!Schema::hasColumn('tasks', 'due_date')) {
            $table->date('due_date')->nullable();
        }

        if (!Schema::hasColumn('tasks', 'start')) {
            $table->dateTime('start')->nullable();
        }

        if (!Schema::hasColumn('tasks', 'end')) {
            $table->dateTime('end')->nullable();
        }

        if (!Schema::hasColumn('tasks', 'is_completed')) {
            $table->boolean('is_completed')->default(false);
        }

        if (!Schema::hasColumn('tasks', 'notified')) {
            $table->boolean('notified')->default(false);
        }
    });
}


public function down()
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->dropColumn(['due_date', 'start', 'end', 'is_completed' , 'notified',]);
    });
}


};
