## Green Bin back-end API
## Technologies
- PHP 8
- Laravel 10
- MySQL
- Postman
## Installation
1. Clone the repository
```bash
git clone https://github.com/AmraneAchraf1/Green-Bin-Backend.git
```
2. Install the dependencies
```bash
composer install
```
3. Create a new database
4. Copy the .env.example file and rename it to .env
5. Update the .env file with your database information
6. Run the migrations
```bash
php artisan migrate
```
7. Link the storage folder
```bash
php artisan storage:link
```
8. Generate the application key
```bash
php artisan key:generate
```
9. Start the server
```bash
php artisan serve
```
10. In .env file, set the following:
```bash
APP_URL=http://localhost:8000
```

## API Documentation
- [Postman Documentation](https://documenter.getpostman.com/view/25366464/2sA35HW1F4)
