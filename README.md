# Mulatham (ملثم)

A real-time multiplayer party game built with Laravel 10 and Laravel Reverb. Players answer questions and try to guess each other's identities through their answers.

## Features

- Real-time gameplay using WebSockets (Laravel Reverb)
- Guest-only authentication (no login required)
- Room-based multiplayer (join via 6-character code)
- Two question types: Text answers & Multiple choice
- Identity guessing with risk/reward mechanics
- Arabic RTL interface with Night Sky theme

## Requirements

- PHP 8.1+
- Composer
- Node.js 18+
- MySQL 5.7+ or MariaDB

## Local Development

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database credentials.

### 3. Database Setup

```bash
php artisan migrate
```

### 4. Run Development Servers

You need three terminal windows:

```bash
# Terminal 1: PHP Server
php artisan serve

# Terminal 2: Vite (frontend assets with HMR)
npm run dev

# Terminal 3: Reverb WebSocket Server
php artisan reverb:start
```

Visit `http://localhost:8000`

---

## Production Deployment (Laravel Forge + DigitalOcean)

### Step 1: Update Production `.env`

Add these variables to your `.env` file on Forge (Site > Environment):

```env
# Broadcasting
BROADCAST_DRIVER=reverb

# Reverb Configuration
REVERB_APP_ID=mulatham
REVERB_APP_KEY=your-random-key-here
REVERB_APP_SECRET=your-random-secret-here
REVERB_HOST="your-domain.com"
REVERB_PORT=443
REVERB_SCHEME=https

# Reverb Server (internal - listens on this port)
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080

# Vite (for built assets)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT=443
VITE_REVERB_SCHEME=https
```

Generate random keys locally:
```bash
php -r "echo bin2hex(random_bytes(16));"
```

### Step 2: Update Forge Deploy Script

Go to **Site > Deployments** and update your deploy script:

```bash
cd /home/forge/your-domain.com

git pull origin main

composer install --no-interaction --prefer-dist --optimize-autoloader

# Install npm dependencies and build assets
npm ci
npm run build

php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart Reverb daemon after deployment
sudo supervisorctl restart reverb-worker:*

( flock -w 10 9 || exit 1
    echo 'Restarting FPM...'; sudo -S service php8.2-fpm reload ) 9>/tmp/fpmlock
```

### Step 3: Add Nginx Configuration for WebSocket

Go to **Site > Nginx Configuration** and add this block **before** the `location /` block:

```nginx
# WebSocket proxy for Reverb
location /app {
    proxy_pass http://127.0.0.1:8080;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_read_timeout 60s;
    proxy_send_timeout 60s;
}
```

Click **Save** (Forge will restart Nginx automatically).

### Step 4: Create Reverb Daemon

Go to **Server > Daemons** and add a new daemon:

| Field | Value |
|-------|-------|
| **Command** | `php artisan reverb:start --host=0.0.0.0 --port=8080` |
| **User** | `forge` |
| **Directory** | `/home/forge/your-domain.com` |
| **Processes** | `1` |
| **Start Seconds** | `1` |

Click **Create Daemon**. Forge will create a supervisor config and start it.

### Step 5: Run Migrations

SSH into your server or use Forge's command runner:

```bash
cd /home/forge/your-domain.com
php artisan migrate
```

### Step 6: Deploy

Trigger a deployment from Forge or push to your `main` branch.

---

## Verification

After deployment, verify everything works:

### Check Reverb is Running
```bash
sudo supervisorctl status
```
Should show: `reverb-worker:reverb-worker_00   RUNNING`

### Check WebSocket Connection
- Open browser DevTools > Network > WS tab
- Visit your site
- You should see a WebSocket connection to `wss://your-domain.com/app/...`

### Check Logs
```bash
# Reverb logs
tail -f /home/forge/.forge/reverb-worker.log

# Laravel logs
tail -f /home/forge/your-domain.com/storage/logs/laravel.log
```

---

## Troubleshooting

### WebSocket Connection Refused
- Check if Reverb daemon is running: `sudo supervisorctl status`
- Restart daemon: `sudo supervisorctl restart reverb-worker:*`

### Mixed Content Error (HTTP/HTTPS)
- Ensure `REVERB_SCHEME=https` and `VITE_REVERB_SCHEME=https` in `.env`
- Rebuild assets: `npm run build`
- Clear config cache: `php artisan config:cache`

### Connection Works Locally but Not in Production
- Check Nginx config has the `/app` location block
- Verify `REVERB_HOST` matches your domain exactly (no `https://` prefix)

### Real-time Updates Not Working
- Check browser console for WebSocket errors
- Verify `BROADCAST_DRIVER=reverb` in `.env`
- Check Reverb logs for connection issues

---

## Game Configuration

Edit `config/game.php` to customize:

```php
return [
    'min_players' => 3,              // Minimum players to start
    'max_questions' => 8,            // Questions per game
    'question_bank_timer' => 60,     // Seconds for question writing
    'answer_timer' => 30,            // Seconds to answer
    'reveal_timer' => 10,            // Seconds for reveal attempt
    'max_questions_per_player' => 5, // Max questions each player can write
];
```

---

## License

This project is proprietary software.
