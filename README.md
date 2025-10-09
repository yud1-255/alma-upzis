# Alma UPZIS

Alma UPZIS is a web application built with the Laravel framework. It provides robust tools for managing Zakat, Infaq, and Sadaqah operations, including user management, transaction tracking, reporting, and more.

## Getting Started

### Prerequisites

- PHP >= 8.0
- Composer
- Node.js & npm
- MySQL or compatible database

### Installation

1. **Clone the repository:**

   ```bash
   git clone https://github.com/yud1-255/alma-app.git
   cd alma-app
   ```

2. **Install PHP dependencies:**

   ```bash
   composer install
   ```

3. **Install JavaScript dependencies:**

   ```bash
   npm install
   ```

4. **Copy and configure environment file:**

   ```bash
   cp .env.example .env
   # Edit .env to set your database and app settings
   ```

5. **Generate application key:**

   ```bash
   php artisan key:generate
   ```

6. **Run migrations and seeders:**

   ```bash
   php artisan migrate --seed
   ```

7. **Build frontend assets:**

   ```bash
   npm run dev
   ```

8. **Start the development server:**
   ```bash
   php artisan serve
   ```

### Running Tests

```bash
php artisan test
```

## Contributing

- Follow Laravel's coding standards.
- Submit pull requests with clear descriptions.
- See the official [Laravel documentation](https://laravel.com/docs) for framework details.

## License

This project is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).
