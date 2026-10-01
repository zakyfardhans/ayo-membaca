# Agent Guide

## Project

- Laravel 12 application for a library catalog, using PHP 8.2+.
- Catalog routes are Indonesian resource routes: `kategori` and `buku` in `routes/web.php`.
- Main code paths: `app/Http/Controllers`, `app/Models`, `database/migrations`, `resources/views/{kategori,buku}`, and `resources/views/layout`.
- Keep user-facing catalog terminology in Indonesian and follow the existing route names and Blade layout conventions.

## Working Conventions

- Check the controller, migration, model, and matching Blade view before changing a catalog field or behavior; the database schema is the source of truth for persisted field names.
- Resource routes do not imply every action is implemented. Inspect the target controller and view before relying on CRUD behavior; several resource actions are placeholders.
- Existing Eloquent model classes are plural (`Books`, `Categories`) and specify their table names. Preserve current class names unless a task explicitly includes a coordinated rename.
- Add or update focused feature tests for changed HTTP behavior. PHPUnit uses in-memory SQLite as configured in `phpunit.xml`.
- The README is the generic Laravel starter README; do not treat it as documentation of app-specific behavior.

## Commands

- Run the test suite: `composer test` (or `php artisan test`).
- Build frontend assets: `npm run build`.
- Start the frontend dev server: `npm run dev`.
- Start the Laravel, queue, log, and Vite processes together: `composer dev`.
