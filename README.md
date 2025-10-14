<p align="center">
  <a href="https://aureuserp.com">
    <picture>
      <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/aureuserp/temp-media/master/aureus-logo-dark.png">
      <source media="(prefers-color-scheme: light)" srcset="https://raw.githubusercontent.com/aureuserp/temp-media/master/aureus-logo-light.png">
      <img src="https://raw.githubusercontent.com/aureuserp/temp-media/master/aureus-logo-light.png" alt="AureusERP logo">
    </picture>
  </a>  
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
-   **FilamentPHP**: Version 4.x, for a seamless and modern admin panel experience.
-   **Database**: MySQL 8.0+ or SQLite for database management.
-   **Composer**: Latest version, to manage PHP dependencies.
-   **Node.js & NPM**: Latest stable versions for compiling front-end assets.
-   **Server**: Apache/Nginx with required PHP extensions (e.g., OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON).
-   **Browser**: A modern browser (Chrome, Firefox, Edge) for accessing the admin panel.

### Installation & Configuration

Installing and setting up Aureus ERP is quick and straightforward. Follow the steps below to get started:

1. **Run the Installation Command**  
   Simply execute the following command in your terminal:

    ```bash
    php artisan erp:install
    ```

2. **What Happens During Installation**:

    - **Migrations & Seeders**:
        - All migrations and seeders from the core or base Laravel project are executed to set up the database schema and populate initial data.
    - **Roles & Permissions**:
        - The `Filament Shield` package automatically generates roles and permissions for the application.
    - **Database Seeders**:
        - Additional seeders are generated and executed to ensure the database is fully populated with the required default configurations.

3. **Admin Account Setup**

    - After the installation process, the command prompts you to provide **Admin Login Credentials** (email and password).
    - These credentials are used to log in to the admin panel.

4. **Installation Complete**  
   Once the above steps are finished, the installation process is complete, and you can start using Aureus ERP.

That’s it! With just one command, your Aureus ERP environment is ready to use.

## Plugins

AureusERP plugin are divided into two categories:

### Core Plugin (System Plugin)

These plugin are essential components of the system and are installed by default:

| Module     | Description                                       |
| ---------- | ------------------------------------------------- |
| Analytics  | Business intelligence and reporting tools         |
| Chatter    | Internal communication and collaboration platform |
| Fields     | Customizable data structure management            |
| Security   | Role-based access control and authentication      |
| Support    | Help desk and documentation                       |
| Table View | Customizable data presentation framework          |

### Installable Plugin

These plugin can be installed as needed to extend system functionality:

| Module       | Description                                  |
| ------------ | -------------------------------------------- |
| Blogs        | Manage blogs                                 |
| Accounts     | Financial accounting and reporting           |
| Contacts     | Contact management for customers and vendors |
| Employees    | Employees management                         |
| Inventories  | Inventory and warehouse management           |
| Invoices     | Invoice generation and management            |
| Partners     | Partner relationship management              |
| Payments     | Payment processing and tracking              |
| Products     | Product catalog and management               |
| Projects     | Project planning and management              |
| Purchases    | Procurement and purchase order management    |
| Recruitments | Applicant tracking and hiring                |
| Sales        | Sales pipeline and opportunity management    |
| Timeoffs     | Leave management and tracking                |
| Timesheet    | Employee work hour tracking                  |
| Website      | Website for customer                         |

## Installation and Management

### Installing a Plugin

To install a plugin, use the following command syntax:

```bash
php artisan <plugin-name>:install
```

For example, to install the Inventories plugin:

```bash
php artisan inventories:install
```

During installation, the system will check for dependencies and prompt you if there are any conflicts or prerequisites:

```
This package products is already installed. What would you like to do? [Skip]:
  [0] Reseed
  [1] Skip
  [2] Show Seeders
```

Options:

-   **Reseed**: Reinstall the plugin's seed data
-   **Skip**: Continue without modifying an already installed dependency
-   **Show Seeders**: Display the available data seeders for the plugin

### Uninstalling a Plugin

To remove a plugin, use the following command syntax:

```bash
php artisan <plugin-name>:uninstall
```

For example, to uninstall the Inventories plugin:

```bash
php artisan inventories:uninstall
```

## Module Dependencies

Some plugins require other plugin to function properly. The system will automatically inform you of these dependencies during the installation process and guide you through installing any required components.

## Customization

AureusERP is designed to be highly customizable, allowing you to:

-   Install only the plugin you need
-   Extend existing plugin with custom functionality
-   Create custom dashboards and reports
-   Define user roles and permissions

### License

Aureus ERP is a truly opensource ERP framework which will always be free under the MIT License.

### Security Vulnerabilities

Please don't disclose security vulnerabilities publicly. If you find any security vulnerability in Aureus ERP then please email us: support@webkul.com.
