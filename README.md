# BayanihanCebu: Blockchain-Powered Disaster Relief Platform

<p align="center">
  <img src="https://via.placeholder.com/200" alt="BayanihanCebu Logooo" width="200">
</p>

## About BayanihanCebu

BayanihanCebu is a blockchain-based disaster relief management system designed to facilitate efficient coordination and transparent resource distribution during calamities in Cebu. Built on Laravel and integrated with Lisk blockchain technology, it enables secure, traceable donations and real-time disaster response coordination.

### Key Features

- 🔐 **Secure Authentication System**
    - Role-based access control (Resident, BDRRMC, LDRRMO, Admin)
    - Barangay-specific user management

- 🌊 **Disaster Management**
    - Real-time disaster reporting
    - Severity level tracking
    - Geographic information system integration

- 💰 **Transparent Donations**
    - Blockchain-verified transactions
    - Real-time donation tracking
    - Automated receipt generation

- 📊 **Resource Management**
    - Resource request system
    - Resource matching algorithm
    - Inventory tracking

## Getting Started

### Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js & NPM
- MySQL/PostgreSQL
- (Future) Lisk SDK

### Installation

1. Clone the repository:

```bash
git clone https://github.com/carlreyyy/BayanihanCebu.git
```

2. Install PHP dependencies:

```bash
composer install
```

3. Install NPM packages:

```bash
npm install
```

4. Create environment file:

```bash
cp .env.example .env
```

5. Generate application key:

```bash
php artisan key:generate
```

6. Configure your database in `.env`

7. Run migrations:

```bash
php artisan migrate
```

8. Start the development server:

```bash
php artisan serve
```

## Project Structure

- `app/` - Contains the core code of the application
- `database/migrations/` - Database structure
- `resources/views/` - Frontend templates
- `routes/` - Application routes
- `tests/` - Automated tests

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Contact

Project Link: [https://github.com/carlreyyy/BayanihanCebu](https://github.com/carlreyyy/BayanihanCebu)
