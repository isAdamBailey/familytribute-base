<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Jetstream removal (issue #19 Phase 6): the app has always been single-team
 * (EnsureHasTeam auto-joined every user to the one team), and with the
 * Inertia frontend gone there's no more session-based UI depending on
 * current_team_id or profile photos. Drops the now-unused team tables and
 * user columns rather than editing the historical migrations that created
 * them.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('team_invitations');
        Schema::dropIfExists('team_user');
        Schema::dropIfExists('teams');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['current_team_id', 'profile_photo_path']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
        });

        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->string('name');
            $table->boolean('personal_team');
            $table->timestamps();
        });

        Schema::create('team_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id');
            $table->foreignId('user_id');
            $table->string('role')->nullable();
            $table->timestamps();

            $table->unique(['team_id', 'user_id']);
        });

        Schema::create('team_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('role')->nullable();
            $table->timestamps();

            $table->unique(['team_id', 'email']);
        });
    }
};
