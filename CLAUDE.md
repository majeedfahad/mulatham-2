# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Mulatham (ملثم) is a Laravel 10 session-based party game where players guess each other's identities through knowledge questions. Players join with a real name and a secret fake name (alias). During gameplay, answers are shown with fake names only, and players try to reveal who is behind each alias.

**Key Features:**
- Guest-only authentication (no login required, session-based)
- Real-time updates using Laravel Reverb WebSockets
- Room-based multiplayer with shareable join codes
- Configurable game settings (timer duration, max questions)
- Player presence detection via heartbeat system
- Ghost players (revealed players continue answering for misdirection)

## Development Commands

```bash
# Install dependencies
composer install
npm install

# Development servers
php artisan serve          # PHP server at http://localhost:8000
npm run dev                # Vite dev server with HMR (run alongside artisan serve)
php artisan reverb:start   # WebSocket server for real-time updates

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
- `app/Http/Controllers/GameController.php` - Main game logic (rooms, gameplay, reveals)
- `app/Models/` - Eloquent models for the game system
- `app/Events/` - Broadcast events for real-time updates
- `resources/views/game/` - Game UI templates (landing, lobby, play, results)
- `routes/web.php` - All application routes
- `config/game.php` - Game configuration (timers, player limits)

### Route Structure
```
# Public routes (no auth required)
GET  /                     - Landing page (create/join room)
POST /room/create          - Create new room
POST /room/join            - Join existing room
GET  /room/{code}          - Lobby (waiting room)

# Game routes (session-based)
POST /room/{code}/ready    - Toggle ready status
POST /room/{code}/start    - Start game (host only)
GET  /room/{code}/play     - Main gameplay screen
POST /room/{code}/answer   - Submit answer
POST /room/{code}/reveal   - Attempt to reveal a player
GET  /room/{code}/results  - Final results/leaderboard

# Player management
POST /room/{code}/heartbeat     - Player presence ping (every 10s)
POST /room/{code}/kick/{id}     - Kick player (host only)
```

### Database Models (New Game System)
- **Room** - Game sessions with code, status, phase, settings
- **RoomPlayer** - Players in a room (name, fake_name, score, status, last_seen_at)
- **RoomQuestion** - Questions submitted by players or from bank
- **RoomAnswer** - Player answers to questions
- **Reveal** - Reveal attempts tracking (guesser, target, result)

### Game Flow
```
Landing → Lobby (ready up) → Question Bank Phase → Answering → Revealing → Results
                                    ↑                              |
                                    └──────── Next Question ───────┘
```

### Game Phases
1. **lobby** - Players join and ready up
2. **question_bank** - Players write questions (configurable timer: 30-180s)
3. **answering** - Players answer the current question
4. **revealing** - Players can attempt to reveal identities
5. **finished** - Game ends, results shown

### Scoring Rules
- **Correct answer**: +1 point (hidden until end)
- **Correct reveal**: Target's points → Guesser, target is revealed
- **Wrong reveal**: Guesser's points → Target, guesser is OUT
- **Winners**: Only unrevealed (hidden) players can win

### Player Presence System
- Heartbeat every 10 seconds from client
- Player considered offline if no heartbeat for 20+ seconds
- Offline players excluded from answer count checks
- Host can kick offline players
- Game ends if online active players drop below minimum (default: 2)

### Frontend Stack
- Blade templates with Bootstrap 5.2
- Bootstrap Icons for iconography
- Laravel Echo + Pusher.js for WebSocket client
- Vite for asset compilation (SASS + JS)
- Custom game CSS with dark theme (night sky aesthetic)

### Configuration (config/game.php)
```php
'question_bank_timer' => 60,      // Default question writing time (seconds)
'answer_timer' => 30,             // Time to answer questions
'reveal_timer' => 45,             // Time for reveal phase
'min_players' => 3,               // Minimum to start
'max_players' => 20,              // Maximum per room
'min_players_to_end' => 2,        // End game when this many remain
```

## Key Implementation Details

### Session-Based Authentication
Players are identified by a `session_token` stored in browser session. No user accounts required.

### Real-time Updates
Using Laravel Reverb for WebSocket broadcasting. Events are broadcast on channel `room.{code}`.

### Online Status Detection
```php
// RoomPlayer model
public function isOnline(): bool {
    return $this->last_seen_at && $this->last_seen_at->diffInSeconds(now()) < 20;
}
```

### Answer Phase Completion
Only online players are counted when checking if all players answered, preventing stuck games when players disconnect.

## Monitoring & Analytics

### Laravel Pulse (Application Monitoring)
- Dashboard accessible at `/pulse` (admin-only)
- Monitors: slow queries, slow requests, exceptions, cache interactions, queue jobs
- Config: `config/pulse.php`

### Sentry (Error Tracking)
- Real-time error tracking and performance monitoring
- Config: `config/sentry.php`
- Set `SENTRY_LARAVEL_DSN` in `.env` to enable

### PostHog (Product Analytics)
- User behavior analytics and feature flags
- Config: `config/posthog.php`
- Service: `App\Services\PostHogService`
- Set `POSTHOG_API_KEY` and `POSTHOG_ENABLED=true` in `.env` to enable
- Frontend tracking script auto-included in game layout

### Admin Panel (Filament)
- Dashboard at `/admin` with:
  - Game statistics widgets (rooms, players, questions)
  - Charts for games and players over time
  - Recent games table
  - Top voted suggestions table
  - Suggestions by category chart
  - Link to Pulse dashboard

### Telegram Notifications
- Service: `App\Services\TelegramService`
- Config: `config/telegram.php`
- Notifications:
  - Game started (automatic when a game begins)
  - Daily report (scheduled at 11 PM Saudi time)
  - Sentry error alerts (via webhook)
- Webhook endpoint: `POST /webhooks/sentry` (for Sentry integration)
- Commands:
  - `php artisan telegram:daily-report` - Manually send daily stats

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3.27
- filament/filament (FILAMENT) - v3
- laravel/framework (LARAVEL) - v10
- laravel/prompts (PROMPTS) - v0
- laravel/reverb (REVERB) - v1
- laravel/sanctum (SANCTUM) - v3
- livewire/livewire (LIVEWIRE) - v3
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v10
- laravel-echo (ECHO) - v2

## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double-check the available parameters.

## URLs
- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches when dealing with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The `search-docs` tool is perfect for all Laravel-related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless there is something very complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v10 rules ===

## Laravel 10

- Use the `search-docs` tool to get version-specific documentation.
- Middleware typically live in `app/Http/Middleware/` and service providers in `app/Providers/`.
- Laravel 10 has a `bootstrap/app.php` file that creates the application instance and binds kernel contracts, but does not use it for application configuration like Laravel 11:
    - Middleware registration is in `app/Http/Kernel.php`
    - Exception handling is in `app/Exceptions/Handler.php`
    - Console commands and schedule registration is in `app/Console/Kernel.php`
    - Rate limits likely exist in `RouteServiceProvider` or `app/Http/Kernel.php`
- When using Eloquent model casts, you must use `protected $casts = [];` and not the `casts()` method. The `casts()` method isn't available on models in Laravel 10.

=== livewire/core rules ===

## Livewire

- Use the `search-docs` tool to find exact version-specific documentation for how to write Livewire and Livewire tests.
- Use the `php artisan make:livewire [Posts\CreatePost]` Artisan command to create new components.
- State should live on the server, with the UI reflecting it.
- All Livewire requests hit the Laravel backend; they're like regular HTTP requests. Always validate form data and run authorization checks in Livewire actions.

## Livewire Best Practices
- Livewire components require a single root element.
- Use `wire:loading` and `wire:dirty` for delightful loading states.
- Add `wire:key` in loops:

    ```blade
    @foreach ($items as $item)
        <div wire:key="item-{{ $item->id }}">
            {{ $item->name }}
        </div>
    @endforeach
    ```

- Prefer lifecycle hooks like `mount()`, `updatedFoo()` for initialization and reactive side effects:

<code-snippet name="Lifecycle Hook Examples" lang="php">
    public function mount(User $user) { $this->user = $user; }
    public function updatedSearch() { $this->resetPage(); }
</code-snippet>

## Testing Livewire

<code-snippet name="Example Livewire Component Test" lang="php">
    Livewire::test(Counter::class)
        ->assertSet('count', 0)
        ->call('increment')
        ->assertSet('count', 1)
        ->assertSee(1)
        ->assertStatus(200);
</code-snippet>

<code-snippet name="Testing Livewire Component Exists on Page" lang="php">
    $this->get('/posts/create')
    ->assertSeeLivewire(CreatePost::class);
</code-snippet>

=== livewire/v3 rules ===

## Livewire 3

### Key Changes From Livewire 2
- These things changed in Livewire 3, but may not have been updated in this application. Verify this application's setup to ensure you conform with application conventions.
    - Use `wire:model.live` for real-time updates, `wire:model` is now deferred by default.
    - Components now use the `App\Livewire` namespace (not `App\Http\Livewire`).
    - Use `$this->dispatch()` to dispatch events (not `emit` or `dispatchBrowserEvent`).
    - Use the `components.layouts.app` view as the typical layout path (not `layouts.app`).

### New Directives
- `wire:show`, `wire:transition`, `wire:cloak`, `wire:offline`, `wire:target` are available for use. Use the documentation to find usage examples.

### Alpine
- Alpine is now included with Livewire; don't manually include Alpine.js.
- Plugins included with Alpine: persist, intersect, collapse, and focus.

### Lifecycle Hooks
- You can listen for `livewire:init` to hook into Livewire initialization, and `fail.status === 419` for the page expiring:

<code-snippet name="Livewire Init Hook Example" lang="js">
document.addEventListener('livewire:init', function () {
    Livewire.hook('request', ({ fail }) => {
        if (fail && fail.status === 419) {
            alert('Your session expired');
        }
    });

    Livewire.hook('message.failed', (message, component) => {
        console.error(message);
    });
});
</code-snippet>

=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.

=== phpunit/core rules ===

## PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should test all of the happy paths, failure paths, and weird paths.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

### Running Tests
- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).
</laravel-boost-guidelines>
