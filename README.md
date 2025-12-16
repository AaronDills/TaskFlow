# Todo App

A task management application built with Laravel 12 and Laravel Breeze.

## Features

- **Tasks** - Create tasks with priorities (high/med/low), due dates, deadlines, and scheduling
- **Subtasks** - Break down tasks into smaller subtasks with automatic parent completion tracking
- **Projects** - Organize tasks into projects with status tracking (Ready to Begin, In Progress, On Hold, Done)
- **Labels** - Color-coded labels for categorizing projects
- **Events** - Calendar events with recurrence support
- **User Authentication** - Full auth system via Laravel Breeze

## Requirements

- PHP 8.2+
- Node.js
- Composer

## Installation

```bash
composer setup
```

This will install dependencies, generate an app key, run migrations, and build frontend assets.

## Development

Start the development server with all services:

```bash
composer dev
```

This runs concurrently:
- Laravel development server
- Queue worker
- Log viewer (Pail)
- Vite dev server

## Testing

```bash
composer test
```

## Tech Stack

- **Backend**: Laravel 12
- **Authentication**: Laravel Breeze
- **Frontend**: Blade, Alpine.js, Tailwind CSS
- **Build Tool**: Vite
