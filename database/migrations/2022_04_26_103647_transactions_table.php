<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions_table', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('students_id');
            $table->string('amount_paid');
            $table->string('mode_of_payment');
            $table->foreign('students_id')
                 ->references('id')->on('enrollment_table')->onDelete('cascade');
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions_table');
    }
}
