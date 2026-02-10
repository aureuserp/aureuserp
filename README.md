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

<h1 align="center">Aureus ERP</h1>

<p align="center">
  <strong>Open-Source Enterprise Resource Planning for Modern Businesses</strong>
</p>

<p align="center">
  Built with Laravel 11 • Powered by FilamentPHP 4 • PHP 8.2+
</p>

---

## 📋 Table of Contents

1. [Introduction](#-introduction)
2. [Key Features](#-key-features)
3. [Why Choose Aureus ERP?](#-why-choose-aureus-erp)
4. [Requirements](#-requirements)
5. [Quick Start](#-quick-start)
6. [Plugin System](#-plugin-system)
7. [Plugin Installation & Management](#-plugin-installation--management)
8. [Customization](#-customization)
9. [Contributing](#-contributing)
10. [License](#-license)
11. [Security](#-security)
12. [Support & Community](#-support--community)

---

## 🚀 Introduction

Aureus ERP is a comprehensive, open-source Enterprise Resource Planning (ERP) solution designed for Small and Medium Enterprises (SMEs) and large-scale organizations. Built on **[Laravel 11](https://laravel.com)**, the most popular PHP framework, and **[FilamentPHP 4](https://filamentphp.com)**, a cutting-edge admin panel framework, Aureus ERP offers an extensible and developer-friendly platform for managing every aspect of your business operations.

Whether you're managing accounting, inventory, HR, CRM, or projects, Aureus ERP provides a modular approach that grows with your business.

---

## ✨ Key Features

-   🏗️ **Modern Architecture**: Built with Laravel 11 and FilamentPHP 4 for maximum performance and developer experience
-   🧩 **Modular Plugin System**: Install only the features you need - from accounting to project management
-   🎨 **Beautiful UI/UX**: Responsive design with TailwindCSS 4, optimized for desktop and mobile
-   🔐 **Advanced Security**: Role-based access control with Filament Shield integration
-   📊 **Business Intelligence**: Built-in analytics and reporting tools
-   🌐 **Multi-Language Support**: Easily translate and localize for global businesses
-   ⚡ **High Performance**: Optimized database queries and caching strategies
-    🔧 **Developer-Friendly**: Clean code, comprehensive documentation, and extensive APIs
-   🔄 **Real-Time Updates**: LiveWire 3 integration for dynamic interfaces

---

## 🎯 Why Choose Aureus ERP?

| Feature | Benefit |
|---------|---------|
| **Open Source** | Free to use, modify, and extend. No vendor lock-in |
| **Modern Stack** | Latest Laravel & FilamentPHP for cutting-edge features |
| **Scalable** | Handles everything from startups to enterprise operations |
| **Customizable** | Extend with your own plugins and modifications |
| **Community-Driven** | Active community support and continuous improvements |
| **Production-Ready** | Battle-tested with real-world business requirements |

---

## 📦 Requirements

Ensure your development environment meets the following requirements:

### Server Requirements
-   **PHP**: 8.2 or higher
-   **Database**: MySQL 8.0+ or SQLite 3.8.3+
-   **Web Server**: Apache 2.4+ or Nginx 1.18+

### Development Tools
-   **Composer**: Latest version (2.0+)
-   **Node.js**: 18.x or higher
-   **NPM/Yarn**: Latest stable version

### Framework Versions
-   **Laravel**: 11.x
-   **FilamentPHP**: 4.x
-   **Livewire**: 3.x
-   **TailwindCSS**: 4.x

---

## ⚡ Quick Start

Get Aureus ERP up and running in just 4 simple steps:

### Step 1: Clone the Repository

```bash
git clone https://github.com/aureuserp/aureuserp.git
cd aureuserp
```

### Step 2: Install Dependencies

```bash
composer install
```

### Step 3: Run the Installation

```bash
php artisan erp:install
```

**What happens during installation:**

✅ Database migrations are executed  
✅ Core seeders populate initial data  
✅ Roles & permissions are generated (via Filament Shield)  
✅ Admin account is created  
✅ Environment configuration is set up

### Step 4: Start the Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000` and log in with your admin credentials!

---

## 🧩 Plugin System

AureusERP features a powerful modular plugin system that allows you to customize your ERP installation based on your business needs. Choose only the modules you need to keep your system lean and efficient.

### 📦 Core Plugins (System Plugins)

These plugins are essential components of the system and are installed by default:

| Module     | Description                                       |
| ---------- | ------------------------------------------------- |
| Analytics  | Business intelligence and reporting tools         |
| Chatter    | Internal communication and collaboration platform |
| Fields     | Customizable data structure management            |
| Security   | Role-based access control and authentication      |
| Support    | Help desk and documentation                       |
| Table View | Customizable data presentation framework          |

### ⚡ Installable Plugins

These plugins can be installed as needed to extend system functionality:

#### 💼 Financial Management
| Module     | Description                           |
| ---------- | ------------------------------------- |
| Accounting | Financial accounting and reporting    |
| Accounts   | Core accounting operations            |
| Invoices   | Invoice generation and management     |
| Payments   | Payment processing and tracking       |

#### 📦 Operations
| Module      | Description                               |
| ----------- | ----------------------------------------- |
| Inventories | Inventory and warehouse management        |
| Products    | Product catalog and management            |
| Purchases   | Procurement and purchase order management |
| Sales       | Sales pipeline and opportunity management |

#### 👥 Human Resources
| Module       | Description                       |
| ------------ | --------------------------------- |
| Employees    | Employee management               |
| Recruitments | Applicant tracking and hiring     |
| Timeoffs     | Leave management and tracking     |
| Timesheet    | Employee work hour tracking       |

#### 🤝 Customer & Partner Management
| Module   | Description                                  |
| -------- | -------------------------------------------- |
| Contacts | Contact management for customers and vendors |
| Partners | Partner relationship management              |

#### 📊 Project & Content Management
| Module   | Description                     |
| -------- | ------------------------------- |
| Blogs    | Content management and blogging |
| Projects | Project planning and management |
| Website  | Customer-facing website module  |

---

## 🔧 Plugin Installation & Management

### Installing a Plugin

To install a plugin, use the following Artisan command:

```bash
php artisan <plugin-name>:install
```

**Example:** Install the Inventories plugin

```bash
php artisan inventories:install
```

During installation, the system automatically checks for dependencies. If dependencies are detected, you'll see:

```
This package products is already installed. What would you like to do? [Skip]:
  [0] Reseed
  [1] Skip
  [2] Show Seeders
```

**Options:**
- **Reseed**: Reinstall the plugin's seed data (overwrites existing data)
- **Skip**: Continue without modifying the already installed dependency
- **Show Seeders**: Display available data seeders for the plugin

### Uninstalling a Plugin

To remove a plugin from your system:

```bash
php artisan <plugin-name>:uninstall
```

**Example:** Uninstall the Inventories plugin

```bash
php artisan inventories:uninstall
```

⚠️ **Warning:** Uninstalling a plugin will remove its database tables and data. Make sure to backup your data before uninstalling.

### Plugin Dependencies

Some plugins require other plugins to function properly. The installation system:
- ✅ Automatically detects dependencies
- ✅ Prompts you to install required plugins
- ✅ Prevents conflicts and missing prerequisites
- ✅ Validates the installation order

---

## 🎨 Customization

AureusERP is designed to be highly customizable, allowing you to tailor the system to your specific business needs:

### Plugin Customization
- 🔹 Install only the plugins you need
- 🔹 Extend existing plugins with custom functionality
- 🔹 Create custom plugins using the modular architecture

### UI/UX Customization
- 🔹 Create custom dashboards and reports
- 🔹 Modify themes and branding
- 🔹 Design custom forms and views with Filament

### Access Control
- 🔹 Define custom user roles and permissions
- 🔹 Configure role-based access control (RBAC)
- 🔹 Set granular permissions using Filament Shield

### Business Logic
- 🔹 Extend models with custom business rules
- 🔹 Create custom workflows and automations
- 🔹 Integrate with third-party services via APIs

---

## 🤝 Contributing

We welcome contributions from the community! Whether you're fixing bugs, adding features, or improving documentation, your help is appreciated.

### How to Contribute

1. **Fork the Repository**
   ```bash
   git clone https://github.com/aureuserp/aureuserp.git
   ```

2. **Create a Feature Branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

3. **Make Your Changes**
   - Follow the existing code style and conventions
   - Write tests for new features
   - Update documentation as needed

4. **Commit Your Changes**
   ```bash
   git commit -m "Add: Brief description of your changes"
   ```

5. **Push to Your Fork**
   ```bash
   git push origin feature/your-feature-name
   ```

6. **Submit a Pull Request**
   - Provide a clear description of the changes
   - Reference any related issues
   - Ensure all tests pass

### Development Guidelines
- Follow Laravel and Filament best practices
- Maintain code quality with Laravel Pint: `vendor/bin/pint`
- Write PHPUnit tests for new functionality
- Use meaningful commit messages

---

## 📄 License

AureusERP is truly open-source ERP framework that will always be **free** under the [MIT License](LICENSE).

### What This Means
- ✅ Free to use for commercial and personal projects
- ✅ Modify and distribute as you wish
- ✅ No licensing fees or restrictions
- ✅ Community-driven development

---

## 🔒 Security

Security is a top priority for AureusERP. We take all security vulnerabilities seriously.

### Reporting Security Vulnerabilities

**⚠️ Please DO NOT disclose security vulnerabilities publicly.**

If you discover a security vulnerability in AureusERP, please report it responsibly:

📧 **Email:** support@webkul.com

### What to Include
- Description of the vulnerability
- Steps to reproduce the issue
- Potential impact assessment
- Suggested fix (if available)

We will acknowledge your email within 48 hours and provide a detailed response within 7 days, including our assessment and a timeline for a fix.

---

## 💬 Support & Community

Need help or want to connect with other AureusERP users?

### 📚 Documentation
- 📖 **Developer Documentation:** [devdocs.aureuserp.com](https://devdocs.aureuserp.com/) - Technical guides, API references, and development resources
- 📘 **User Guide:** [docs.aureuserp.com](https://docs.aureuserp.com/) - Step-by-step tutorials and user manuals

### 🤝 Get Support
- 💬 **Community Forum:** [Coming Soon] - Connect with other users and share experiences
- 🐛 **Issue Tracker:** [GitHub Issues](https://github.com/aureuserp/aureuserp/issues) - Report bugs and request features
- 📧 **Email Support:** support@webkul.com - Direct support from the team

### 🔔 Stay Updated
- ⭐ **Star** this repository to show your support
- 👁️ **Watch** for new releases and updates
- 🍴 **Fork** to contribute to the project

---

<div align="center">

Made with ❤️ by [Webkul](https://webkul.com)

[⬆ Back to Top](#-table-of-contents)

</div>
