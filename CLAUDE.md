# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Mulatham is a Laravel 10 quiz/trivia game application where users answer multiple-choice questions. Features include user authentication, question management with images, answer tracking, elimination/skip functionality, and anonymous play via fake name generation.

## Development Commands

```bash
# Install dependencies
composer install
npm install

# Development servers
php artisan serve          # PHP server at http://localhost:8000
npm run dev                # Vite dev server with HMR (run alongside artisan serve)

# Database
php artisan migrate        # Run migrations
php artisan migrate:fresh  # Reset and re-run all migrations
php artisan db:seed        # Seed database

# Testing
php artisan test                    # Run all tests
php artisan test tests/Unit/        # Run unit tests only
php artisan test tests/Feature/     # Run feature tests only
php artisan test --filter=TestName  # Run specific test

# Code style
./vendor/bin/pint          # Fix PHP code style (Laravel Pint)

# Production build
npm run build              # Compile assets for production
```

## Architecture

### Key Directories
- `app/Http/Controllers/` - Route handlers (HomeController for game logic, QuestionController for admin CRUD)
- `app/Models/` - Eloquent models: User, Question, Answer, AnswerUser (pivot), Elimination, Setting
- `resources/views/` - Blade templates organized by feature (admin/, questions/, answers/, auth/)
- `routes/web.php` - All application routes

### Route Structure
- Public: `/` (welcome), `/fakename` (anonymous name)
- Authenticated: `/home` (game dashboard), `/question/{id}`, `/users`
- Admin (Settings middleware): `/Settings/*` - admin dashboard, question CRUD, user management

### Database Models
- **Question** - Quiz questions with optional image support
- **Answer** - Multiple choice options linked to questions
- **AnswerUser** - Pivot tracking user responses to questions
- **Elimination** - Tracks skipped/eliminated questions per user
- **Setting** - Application configuration

### Frontend Stack
- Blade templates with Bootstrap 5.2
- Vite for asset compilation (SASS + JS)
- Axios for HTTP requests
