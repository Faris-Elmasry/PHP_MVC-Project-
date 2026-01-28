# 🚀 Invoice & Client Management System

![PHP Badge](https://img.shields.io/badge/PHP-8.4+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![License Badge](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)
![Status Badge](https://img.shields.io/badge/Status-Live-success?style=for-the-badge)

A powerful, lightweight web application designed to streamline business operations. Built on top of a **high-performance custom PHP MVC framework**, this project demonstrates advanced architectural patterns without the overhead of heavy third-party frameworks.

🔗 **[Live Demo](https://mvc-php-project-webiste-wi2efq-7c79b4-195-201-218-51.traefik.me/)**

---

## ✨ Key Features

### 🏢 Application Modules
- **Dashboard Overview**: Real-time insights into your business performance.
- **Client Management**: specific CRUD operations to manage client details efficiently.
- **Product Inventory**: Track products, prices, and stock levels.
- **Invoicing System**: Generate, edit, and manage professional invoices with ease.
- **Authentication**: Secure user login and registration system.

### 🛠️ Technical Highlights (Custom Framework)
 This project runs on a bespoke MVC framework engineered for speed and simplicity:
- **Custom Routing Engine**: Flexible routing with support for closures and controller actions.
- **Native ORM & Query Builder**: specialized abstraction layer for database interactions.
- **Database Migrations & Seeding**: Version control for your database schema.
- **Middleware & Validation**: Robust request filtering and input validation.
- **View Engine**: Clean separation of logic and presentation.

---

## 🏗️ Technology Stack

- **Backend**: [PHP 8.4](https://www.php.net/releases/8.4/en.php)
- **Database**: MySQL
- **Containerization**: Docker & Nixpacks
- **Frontend**: HTML5, CSS, JavaScript

---

## 🚀 Getting Started

Follow these steps to set up the project locally.

### Prerequisites
- PHP >= 8.4
- Composer
- MySQL

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/your-repo-name.git
   cd your-repo-name
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Environment Setup**
   Copy the example environment file and configure your database credentials.
   ```bash
   cp .env.example .env
   # Edit .env with your DB settings
   ```

4. **Database Setup**
   Run the custom migration command to set up tables.
   ```bash
   php app migrate
   php app db:seed
   ```

5. **Serve the Application**
   ```bash
   php -S localhost:8000 -t public
   ```
   Visit `http://localhost:8000` in your browser.

---

## 📂 Project Structure

```
├── App/                # Application Core (Controllers, Models)
├── config/             # Configuration files
├── public/             # Entry point (index.php) and assets
├── routes/             # Web routes definition
├── src/                # Framework Core (My MVC Implementation)
├── views/              # Front-end Templates
└── ...
```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
