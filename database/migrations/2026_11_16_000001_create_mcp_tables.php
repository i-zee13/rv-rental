<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mcp_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('token_hash', 64)->unique();
            $table->string('token_prefix', 24)->unique();
            $table->string('workspace_id', 100);
            $table->unsignedBigInteger('user_id');
            $table->json('scopes');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('workspace_id');
        });

        Schema::create('mcp_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mcp_access_token_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('workspace_id', 100)->nullable();
            $table->string('method', 80)->nullable();
            $table->string('tool', 80)->nullable();
            $table->string('outcome', 40);
            $table->unsignedSmallInteger('http_status');
            $table->string('ip_address', 45)->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->string('message', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['mcp_access_token_id', 'created_at']);
            $table->index('outcome');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcp_audit_logs');
        Schema::dropIfExists('mcp_access_tokens');
    }
};
