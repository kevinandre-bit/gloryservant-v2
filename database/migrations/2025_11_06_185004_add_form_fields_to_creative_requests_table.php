<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFormFieldsToCreativeRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_creative_requests', function (Blueprint $table) {
            $table->json('form_data')->nullable()->after('description');
            $table->boolean('admin_approved')->default(false)->after('status');
            $table->string('requester_name')->nullable()->after('requester_people_id');
            $table->string('requester_ministry')->nullable()->after('requester_name');
            $table->string('requester_email')->nullable()->after('requester_ministry');
        });
    }

    public function down()
    {
        Schema::table('tbl_creative_requests', function (Blueprint $table) {
            $table->dropColumn(['form_data', 'admin_approved', 'requester_name', 'requester_ministry', 'requester_email']);
        });
    }
}
