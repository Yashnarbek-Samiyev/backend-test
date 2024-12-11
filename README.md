# Product Materials Calculation API

This project is a Laravel-based API for calculating material requirements for various products. It allows you to calculate the necessary materials (e.g., fabric, buttons, zippers) required for producing specific products such as "Koylak" and "Shim". The API calculates the materials based on the quantity of products ordered, retrieves data from warehouses, and returns the required quantities and prices for each material.

## Features

- Calculate material requirements for different products.
- Support for multiple products, including "Koylak" and "Shim".
- Integration with warehouse data to fetch material availability and pricing.
- Material allocation across multiple warehouses.

## Technologies Used

- **Backend**: Laravel 10
- **Frontend**: Vue.js (if applicable, based on your setup)
- **Database**: MySQL
- **API**: RESTful API for interaction
- **Other**: Docker for containerization (if used in the project)

## Installation

Follow these steps to set up the project locally.

### Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL or any other database supported by Laravel
- Node.js and npm (if working with Vue.js)

### Step 1: Clone the Repository

Clone the repository to your local machine:

```bash
git clone https://github.com/Yashnarbek-Samiyev/backend-test
cd backend-test
```

### Step 2: Install Dependencies
    
Install PHP dependencies:

```bash
composer install
```

Install JavaScript dependencies (if working with Vue.js):

```bash
npm install
```
    
### Step 3: Set Up Environment Variables

Create a new `.env` file by copying the `.env.example` file:

```bash
cp .env.example .env
```
    
Update the `.env` file with your database credentials and other settings.

### Step 4

Run the following command to generate a new application key:

```bash
php artisan key:generate
```
    
### Step 5: Migrate the Database

Run the following command to migrate the database:

```bash
php artisan migrate
```

### Step 6: Start the Development Server

Start the development server:

```bash
php artisan serve
```

