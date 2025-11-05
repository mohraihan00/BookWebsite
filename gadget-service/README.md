# Gadget Service Management System

A professional web application for managing gadget device service lifecycle from customer check-in to device pickup. Built with PHP + MySQL (XAMPP stack) featuring automated ticketing, technician diagnostics, customer approval workflows, inventory management, billing, and warranty tracking.

## 🚀 Features

### Core Functionality
- **Customer Management**: Complete customer registration and history tracking
- **Ticket System**: Automated ticket numbering and workflow management
- **Device Registration**: Comprehensive device information with condition tracking
- **Diagnostic Workflow**: Technician assessment with image documentation
- **Estimate System**: Cost estimation with customer approval process
- **Repair Management**: Complete repair workflow with parts tracking
- **Inventory Management**: Automatic stock management with alerts
- **Payment Processing**: Multiple payment methods with invoicing
- **Warranty Management**: Automated warranty creation and claim tracking
- **Reporting Dashboard**: Comprehensive analytics and reporting

### Advanced Features
- **Role-based Access Control**: Admin, Customer Service, Technician, and Customer roles
- **Audit Trail**: Complete history of all status changes and transactions
- **Automated Notifications**: Email/SMS alerts for status updates
- **File Upload**: Image documentation for diagnostics and repairs
- **Barcode/QR Support**: Ticket and customer code generation
- **Real-time Stock Management**: Automatic deduction via database triggers
- **Business Rule Enforcement**: Stored procedures ensure data integrity

## 📋 System Requirements

- **XAMPP 7.4+** (PHP 7.4+, Apache 2.4+, MySQL 8.0+)
- **PHP Extensions**: mysqli, gd, curl, json, mbstring
- **Browser Support**: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- **Storage**: 10GB minimum (grows with usage)
- **RAM**: 4GB minimum (8GB recommended)

## 🛠 Installation

### 1. Setup Database

1. Start XAMPP and ensure Apache and MySQL are running
2. Create a new database named `gadget_service`
3. Import the database files in order:

```bash
# Navigate to the database directory
cd database/

# Import schema
mysql -u root -p gadget_service < schema.sql

# Import triggers
mysql -u root -p gadget_service < triggers.sql

# Import stored procedures
mysql -u root -p gadget_service < procedures.sql

# Import views
mysql -u root -p gadget_service < views.sql

# Import initial data
mysql -u root -p gadget_service < seed_data.sql
```

### 2. Configure Application

1. Copy the project to your XAMPP htdocs directory
2. Update database credentials in `config/config.php` if needed
3. Ensure the `uploads/` directory is writable by the web server

### 3. Access the Application

Open your browser and navigate to: `http://localhost/gadget-service/`

**Default Login Credentials:**
- **Admin**: username `admin`, password `admin123`
- **Customer Service**: username `customer_service`, password `cs123`
- **Technician**: username `technician1`, password `tech123`

## 📊 Database Architecture

The system uses a 3NF compliant database with the following key features:

### Master Data Tables
- `roles`, `users`, `brands`, `device_models`, `parts`, `status_refs`

### Operational Tables
- `customers`, `devices`, `tickets`, `ticket_status_history`
- `diagnostics`, `estimates`, `estimate_items`
- `repairs`, `repair_parts`, `stock_mutations`
- `payments`, `warranties`, `warranty_claims`

### Key Features
- **Foreign Key Constraints**: Full referential integrity
- **Generated Columns**: Automatic calculations (line totals, warranty end dates)
- **Triggers**: Automatic stock management and audit logging
- **Stored Procedures**: Business logic enforcement
- **Views**: Optimized reporting queries

## 👥 User Roles & Permissions

### Administrator
- Full system access
- User management
- System configuration
- All reports and administrative functions

### Customer Service
- Customer registration and management
- Ticket creation and status tracking
- Basic reporting
- Payment processing

### Technician
- Diagnostic creation and management
- Estimate creation and management
- Repair management and parts usage
- Time tracking and productivity reports

### Customer
- View own tickets and status
- Approve estimates online
- View repair status and history
- Download invoices and warranty certificates

## 🔒 Security Features

- **SQL Injection Prevention**: All queries use prepared statements
- **XSS Protection**: Output sanitization and Content Security Policy
- **CSRF Protection**: Token-based form validation
- **Session Security**: Secure authentication with automatic timeout
- **Password Security**: bcrypt hashing with complexity requirements
- **Audit Logging**: Complete audit trail for sensitive operations

## 📈 Reporting & Analytics

The system includes comprehensive reporting views:

- **Revenue Reports**: Monthly revenue, payment tracking
- **Productivity Reports**: Technician performance metrics
- **Inventory Reports**: Stock levels, usage statistics
- **Turnaround Time**: Service efficiency analysis
- **Customer Analytics**: Customer service history and value

## 🔄 Business Workflows

### 1. Device Check-in
1. CS registers customer and device details
2. System generates automatic ticket number
3. Device assigned to available technician (round-robin)

### 2. Diagnostic Process
1. Technician performs device assessment
2. Creates diagnostic report with images
3. Generates cost estimate
4. Estimate sent to customer for approval

### 3. Repair Process
1. Customer approves estimate
2. Status automatically changes to "REPAIRING"
3. Technician performs repairs
4. Parts automatically deducted from inventory
5. Repair completion logged with before/after images

### 4. Payment & Completion
1. System calculates final cost
2. Payment processed and confirmed
3. Invoice generated and available for download
4. Warranty automatically created (90 days default)
5. Customer notified for pickup

## 🛠️ Customization

### Adding New Device Brands/Models
```sql
-- Add new brand
INSERT INTO brands (brand_name, description) VALUES ('New Brand', 'Description');

-- Add device model
INSERT INTO device_models (brand_id, model_name, model_number, device_type)
VALUES ((SELECT id FROM brands WHERE brand_name = 'New Brand'), 'Model Name', 'Model Number', 'smartphone');
```

### Adding New Parts
```sql
INSERT INTO parts (part_sku, part_name, description, unit_price, stock_qty, min_stock_level)
VALUES ('PART-001', 'Part Name', 'Description', 25.00, 50, 10);
```

### Configuration
Update `config/config.php` for:
- Database credentials
- Company information
- Tax rates and currency
- Email settings
- Security parameters

## 🐛 Troubleshooting

### Common Issues

**Database Connection Error**
- Verify MySQL is running
- Check database credentials in config
- Ensure database exists and is imported correctly

**File Upload Issues**
- Check permissions on uploads directory
- Verify file size limits in php.ini
- Ensure correct MIME types are allowed

**Session Issues**
- Check session.save_path in php.ini
- Verify directory is writable
- Clear browser cookies and cache

**Performance Issues**
- Optimize MySQL indexes
- Enable query cache
- Check server resources

## 📝 Development

### Code Structure
```
gadget-service/
├── config/           # Configuration files
├── includes/         # Core PHP classes and functions
├── pages/            # Page templates by module
├── api/              # AJAX endpoints
├── assets/           # CSS, JS, and images
├── uploads/          # File upload directory
├── templates/        # Document templates
├── database/         # SQL files
└── index.php         # Main entry point
```

### Adding New Features
1. Create database migrations in `database/`
2. Add API endpoints in `api/`
3. Create page templates in `pages/`
4. Update navigation in `includes/header.php` and `includes/sidebar.php`
5. Add role permissions to authentication system

## 📄 License

This project is provided as-is for educational and demonstration purposes.

## 🤝 Support

For technical support:
1. Check the troubleshooting section
2. Review system logs
3. Verify database integrity
4. Check PHP error logs

## 🔄 Version History

### v1.0.0
- Initial release
- Complete ticket management system
- Customer and inventory management
- Payment and warranty systems
- Reporting dashboard
- Role-based access control

---

**System Status**: ✅ Production Ready
**Last Updated**: November 2024
**Framework**: PHP 7.4+, MySQL 8.0+, Bootstrap 5, jQuery