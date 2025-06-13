<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primeiro, remove a tabela subscriptions se ela já existir
        Schema::dropIfExists('dns_record_subscription');
        Schema::dropIfExists('subscriptions');
        
        // Cria a tabela de assinaturas
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('value', 10, 2)->default(0);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active');
            $table->timestamps();
        });
        
        // Cria a tabela pivot entre assinaturas e registros DNS
        Schema::create('dns_record_subscription', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dns_record_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Garante unicidade da associação
            $table->unique(['dns_record_id', 'subscription_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dns_record_subscription');
        Schema::dropIfExists('subscriptions');
    }
};
