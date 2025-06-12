# Banking Application

A aimple banking app with multi currency transfer.

## Features

-  Multi-currency support with real-time exchange rates
-  Secure fund transfers between accounts
-  Transaction history tracking
-  Savings account management
-  Rate limiting for API endpoints
-  TODO::Comprehensive test coverage

## Tech Stack

### Backend
- PHP 8.2+
- Laravel 12
- MySQL 8.1

### Frontend
- React
- Inertia.js

## Prerequisites

- PHP 8.3 or higher
- Composer
- Node.js 22 or higher
- MySQL 8.3 or higher

## Installation


1. Install PHP dependencies:
```bash
composer install
```

2. Install JavaScript dependencies:
```bash
npm install
```

3. Copy the environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure your database in `.env`:


7. Run migrations:
```bash
php artisan migrate
```

8. Start the development server:
```bash
php artisan serve
```

9. In a separate terminal, start the Vite development server:
```bash
npm run dev
```

## API Documentation

### Authentication
All API endpoints require authentication using Laravel Sanctum.

### Rate Limiting
- Global API: 60 requests per minute per IP
- Transfers: 10 transfers per minute per sender account

### Endpoints

#### Transfers
```http
POST /api/v1/transfers
Content-Type: application/json

{
    "sender_account_id": 1,
    "recipient_account_number": "SA15497532",
    "amount": 50.00,
    "currency": "USD",
    "description": "Test transfer"
}
```



## Testing

//TODO:: no tests as of yet

## Security

- API endpoints are rate-limited
- Multi-currency transfers are validated
- Transaction history is automatically tracked
- Database transactions ensure data integrity


## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support, email support@yourbankingapp.com or open an issue in the repository. 