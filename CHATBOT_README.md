# Conversational AI Platform Implementation

## Overview
This implementation adds a comprehensive Conversational AI platform to the Yii2 e-commerce system using Rasa Open Source for NLP processing. The system supports multi-tenancy and automates various business processes through natural language interactions.

## Architecture Components

### 1. Multi-Tenant Database Schema
- **tenants**: Stores tenant information and settings
- **chatbot_intents**: Maps intents to automation actions per tenant
- **chatbot_sessions**: Manages conversation sessions
- **chatbot_logs**: Audit trail for all interactions

### 2. Yii2 Module Structure
```
modules/chatbot/
├── Module.php                 # Main module configuration
├── controllers/
│   ├── ApiController.php      # REST API endpoints
│   ├── AdminController.php    # Admin management interface
│   └── WidgetController.php   # Chat widget interface
├── models/
│   ├── ChatbotIntent.php      # Intent definitions
│   ├── ChatbotSession.php     # Session management
│   └── ChatbotLog.php         # Logging
├── services/
│   ├── ChatbotService.php     # Main orchestration service
│   ├── AutomationService.php  # Business logic automation
│   └── NlpService.php         # Rasa integration
├── views/
│   ├── admin/                 # Admin interface
│   └── widget/                # Chat widget
└── assets/                    # CSS/JS assets
```

### 3. Rasa Integration
- **NLP Engine**: Rasa Open Source for intent recognition and entity extraction
- **Training Data**: Pre-configured intents for e-commerce automation
- **API Communication**: HTTP-based integration with Rasa server

## Setup Instructions

### 1. Database Migration
```bash
php yii migrate
```
This will create all necessary tables for multi-tenancy and chatbot functionality.

### 2. Rasa Setup
```bash
# Navigate to rasa directory
cd rasa

# Install Rasa
pip install rasa

# Train the model
rasa train

# Start Rasa server
rasa run --cors "*" --port 5005
```

### 3. Configuration
Update `config/web.php` to include the chatbot module (already done).

### 4. Access Control
The chatbot widget is only shown to logged-in users. Admin functions require admin role.

## API Endpoints

### Chatbot API
- `POST /chatbot/api/message` - Send message and get response
- `POST /chatbot/api/automate` - Direct automation execution
- `GET /chatbot/api/history` - Get conversation history
- `GET /chatbot/api/status` - Check system status

### Admin Interface
- `/chatbot/admin` - Main admin dashboard
- `/chatbot/admin/intents` - Manage intent mappings
- `/chatbot/admin/logs` - View conversation logs
- `/chatbot/admin/settings` - Tenant-specific settings

## Automation Actions

### Available Automations
1. **create_customer** - Register new customer accounts
2. **process_order** - Process shopping cart into orders
3. **check_inventory** - Query product stock levels
4. **get_order_status** - Check order status by ID
5. **assign_license** - Generate and assign license keys
6. **generate_report** - Create business reports

### Customizing Automations
Add new automations by:
1. Creating methods in `AutomationService`
2. Adding intent mappings in `chatbot_intents` table
3. Training Rasa with new intents

## Security Considerations

### Multi-Tenant Isolation
- All queries include tenant_id filtering
- Users can only access their tenant's data
- API requests validate tenant context

### Authentication
- Widget only available to authenticated users
- Admin functions require admin role
- Session-based conversation tracking

### Data Privacy
- Conversation logs stored with tenant isolation
- Configurable data retention policies
- GDPR-compliant logging

## Monitoring and Maintenance

### Logging
- All conversations logged in `chatbot_logs`
- Intent recognition confidence tracking
- Automation execution results

### Performance
- Session caching for conversation history
- Asynchronous processing for heavy operations
- Rate limiting on API endpoints

### Scaling
- Horizontal scaling support through tenant partitioning
- External Rasa server for NLP processing
- Database optimization for large conversation volumes

## Troubleshooting

### Common Issues
1. **Rasa server not responding**: Check if Rasa is running on port 5005
2. **Database connection errors**: Ensure migrations are run
3. **Permission denied**: Check user roles and tenant access

### Debug Mode
Enable debug logging in `config/web.php` for detailed error information.

## Future Enhancements

### Planned Features
- Voice integration
- Multi-language support
- Advanced analytics dashboard
- Integration with external CRM systems
- Custom action development interface

### Performance Optimizations
- Redis caching for sessions
- Elasticsearch for log analytics
- Kubernetes deployment configuration

## Support
For issues or questions, check the logs in `/chatbot/admin/logs` or review the Rasa server logs.