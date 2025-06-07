<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBankFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_fields', function (Blueprint $table) {
            // Renomear colunas antigas para as novas
            if (Schema::hasColumn('bank_fields', 'field_name')) {
                $table->renameColumn('field_name', 'name');
            }
            
            if (Schema::hasColumn('bank_fields', 'field_label')) {
                $table->renameColumn('field_label', 'field_key');
            }
            
            if (Schema::hasColumn('bank_fields', 'required')) {
                $table->renameColumn('required', 'is_required');
            }
            
            // Adicionar coluna de opções se não existir
            if (!Schema::hasColumn('bank_fields', 'options')) {
                $table->text('options')->nullable()->after('field_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_fields', function (Blueprint $table) {
            if (Schema::hasColumn('bank_fields', 'name')) {
                $table->renameColumn('name', 'field_name');
            }
            
            if (Schema::hasColumn('bank_fields', 'field_key')) {
                $table->renameColumn('field_key', 'field_label');
            }
            
            if (Schema::hasColumn('bank_fields', 'is_required')) {
                $table->renameColumn('is_required', 'required');
            }
            
            if (Schema::hasColumn('bank_fields', 'options')) {
                $table->dropColumn('options');
            }
        });
    }
}
