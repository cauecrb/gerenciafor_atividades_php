<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE tasks MODIFY status VARCHAR(191) NOT NULL DEFAULT "pending"');
            return;
        }

        if ($driver === 'sqlite') {
            Schema::disableForeignKeyConstraints();
            DB::statement('CREATE TABLE tasks_temp (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                project_id INTEGER NULL,
                title VARCHAR(255) NOT NULL,
                description TEXT NULL,
                status VARCHAR(255) NOT NULL DEFAULT "pending",
                priority VARCHAR(255) NOT NULL DEFAULT "medium",
                due_at DATETIME NULL,
                completed_at DATETIME NULL,
                attachment_path VARCHAR(255) NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            )');
            DB::statement('INSERT INTO tasks_temp (id, user_id, project_id, title, description, status, priority, due_at, completed_at, attachment_path, created_at, updated_at)
                SELECT id, user_id, project_id, title, description, status, priority, due_at, completed_at, attachment_path, created_at, updated_at FROM tasks');
            DB::statement('DROP TABLE tasks');
            DB::statement('ALTER TABLE tasks_temp RENAME TO tasks');
            Schema::enableForeignKeyConstraints();
            return;
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE tasks MODIFY status ENUM("pending","completed") NOT NULL DEFAULT "pending"');
            return;
        }

        if ($driver === 'sqlite') {
            Schema::disableForeignKeyConstraints();
            DB::statement('CREATE TABLE tasks_temp (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                project_id INTEGER NULL,
                title VARCHAR(255) NOT NULL,
                description TEXT NULL,
                status TEXT NOT NULL DEFAULT "pending",
                due_at DATETIME NULL,
                completed_at DATETIME NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            )');
            DB::statement('INSERT INTO tasks_temp (id, user_id, project_id, title, description, status, due_at, completed_at, created_at, updated_at)
                SELECT id, user_id, project_id, title, description, status, due_at, completed_at, created_at, updated_at FROM tasks');
            DB::statement('DROP TABLE tasks');
            DB::statement('ALTER TABLE tasks_temp RENAME TO tasks');
            Schema::enableForeignKeyConstraints();
            return;
        }
    }
};

