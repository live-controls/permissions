<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('livecontrols_group_userpermissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_group_id')->constrained('livecontrols_user_groups', 'id')->cascadeOnDelete();
            $table->foreignId('user_permission_id')->constrained('livecontrols_user_permissions', 'id')->cascadeOnDelete();
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
        Schema::dropIfExists('livecontrols_group_userpermissions');
    }
};
