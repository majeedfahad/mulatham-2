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
