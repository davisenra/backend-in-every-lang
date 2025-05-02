# PHP Implementation

## Stack

This PHP implementation features a quite minimal setup:

- PHP 8.4 CLI as Runtime
- `react/http` as HTTP Server
- `react/async` to handle async/await
- `clue/reactphp-sqlite` + SQLite as datasource
- `php-di/php-di` as dependency injection
- `monolog/monolog` as logger
- `phpunit/phpunit` as testing framework
- `phpstan/phpstan` as code quality
- `laravel/pint` as code formatter

## Architecture

This implementation differs from traditional PHP-FPM by leveraging
ReactPHP's event loop to create a long-lived HTTP server - similar
to Node.js. The server runs continuously until explicitly stopped.

### Key Components

1. **Bootstrap** (`bin/server.php`)
    - Initializes the application
    - Starts the event loop

2. **Dependency Container** (`src/Definitions.php`)
    - Configures all services including:
        - HTTP Server with middleware/routing stack
        - Socket binding
        - Database connection
        - Logger

3. **Routing**
    - `src/Http/Router.php`: Collects and manages routes
    - `src/Http/Routes.php`: Route definitions
    - Route handlers implement `HttpAction` interface (`src/Http/HttpAction.php`)
    - Dynamic route registration with parameter support
    - Regex-based matching
    - Container-aware action resolution

4. **Server**
    - Binds to specified port
    - Runs indefinitely until process termination

### Flow

SocketServer → HttpServer → Middleware Stack → Router → HttpAction

## How to Run

### Datasource

```bash
# sqlite3 bin is required
touch database.db
sqlite3 database.db < migrations/migrations.sql
sqlite3 database.db < migrations/seed.sql
```

### Application

_Ensure you setup the database before running the app!_

```bash
docker compose build
docker compose up
```
