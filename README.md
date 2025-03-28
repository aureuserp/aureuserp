<p align="center">
<a href="https://aureuserp.com"><img src="https://aureuserp.com/wp-content/uploads/2025/03/logo.png" alt="Total Downloads"></a>
</p>

<p align="center">
<a href="https://packagist.org/packages/aureuserp/aureuserp"><img src="https://poser.pugx.org/aureuserp/aureuserp/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/aureuserp/aureuserp"><img src="https://poser.pugx.org/aureuserp/aureuserp/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/aureuserp/aureuserp"><img src="https://poser.pugx.org/aureuserp/aureuserp/license.svg" alt="License"></a>
</p>

## Topics

1. [Introduction](#introduction)
2. [Requirements](#requirements)
3. [Installation & Configuration](#installation-and-configuration)
4. [License](#license)
5. [Security Vulnerabilities](#security-vulnerabilities)

### Introduction

Aureus ERP is a comprehensive, open-source Enterprise Resource Planning (ERP) solution designed for Small and Medium Enterprises (SMEs) and large-scale enterprises. Built on **[Laravel](https://laravel.com)**, the most popular PHP framework, and **[FilamentPHP](https://filamentphp.com)**, a dynamic resource management library, Aureus ERP offers an extensible and developer-friendly platform for managing every aspect of your business operations.

**Key Features**

-   **Built with Laravel**: Leverages the robust and scalable features of Laravel, ensuring security, reliability, and flexibility for enterprise needs.
-   **Powered by FilamentPHP**: Incorporates FilamentPHP for intuitive resource management, modular forms, and dynamic admin panels.
-   **Highly Modular Design**: Enables seamless integration of custom modules for finance, HR, inventory, CRM, and more.
-   **Open-Source Solution**: Free to use, modify, and extend, fostering community-driven innovation and improvements.
-   **Scalable for Enterprises**: Built to handle complex business processes and workflows, making it suitable for growing organizations.

**Why Choose Aureus ERP?**

-   **Modern Technology Stack**: Combines Laravel's backend strength with FilamentPHP's efficient frontend capabilities.
-   **Developer-Centric Design**: Offers clean code, modular architecture, and extensive documentation for custom development.
-   **User-Friendly Interface**: Features responsive and visually appealing designs with TailwindCSS.
-   **Scalable & Customizable**: Adapts to the unique requirements of businesses of all sizes.
-   **Community-Driven**: Backed by a thriving open-source community for support and innovation.

### Requirements

To run and develop Aureus ERP, ensure your environment meets the following requirements:

-   **PHP**: Version 8.2 or higher.
-   **Laravel**: Version 11.x, for leveraging the latest framework features and improvements.
-   **FilamentPHP**: Version 3.x, for a seamless and modern admin panel experience.
-   **Database**: MySQL 8.0+ or SQLite for database management.
-   **Composer**: Latest version, to manage PHP dependencies.
-   **Node.js & NPM**: Latest stable versions for compiling front-end assets.
-   **Server**: Apache/Nginx with required PHP extensions (e.g., OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON).
-   **Browser**: A modern browser (Chrome, Firefox, Edge) for accessing the admin panel.

### Installation & Configuration

Installing and setting up Aureus ERP is quick and straightforward. Follow the steps below to get started:

1. **Run the Installation Command**
   ```
   git clone https://github.com/aureuserp/aureuserp.git
   cd aureuserp
   Composer install
   type nul > database/database.sqlite
   php artisan key:generate
   ```
3. 
   Simply execute the following command in your terminal:

    ```bash
    php artisan erp:install
    ```

4. **What Happens During Installation**:

    - **Migrations & Seeders**:
        - All migrations and seeders from the core or base Laravel project are executed to set up the database schema and populate initial data.
    - **Roles & Permissions**:
        - The `Filament Shield` package automatically generates roles and permissions for the application.
    - **Database Seeders**:
        - Additional seeders are generated and executed to ensure the database is fully populated with the required default configurations.

5. **Admin Account Setup**

    - After the installation process, the command prompts you to provide **Admin Login Credentials** (email and password).
    - These credentials are used to log in to the admin panel.

6. **Installation Complete**  
   Once the above steps are finished, the installation process is complete, and you can start using Aureus ERP.

That’s it! With just one command, your Aureus ERP environment is ready to use.

### License

Aureus CRM is a truly opensource CRM framework which will always be free under the MIT License.

### Security Vulnerabilities

Please don't disclose security vulnerabilities publicly. If you find any security vulnerability in Aureus CRM then please email us: sales@aureuserp.com.
