<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">E-Commerce Platform with Conversational AI</h1>
    <br>
</p>

Advanced e-commerce platform built with [Yii 2](https://www.yiiframework.com/) and integrated Conversational AI for intelligent automation. This application combines powerful PHP backend with Python-based NLP for natural language processing, enabling automated business workflows through conversational interactions.

**Key Features:**
- Full e-commerce functionality with product management, shopping cart, and order processing
- Intelligent chatbot with entity extraction for natural language queries
- Intent recognition and automated workflow execution
- Multi-tenant architecture with role-based access control (RBAC)
- Real-time inventory management with conversational queries
- Advanced order management and customer relationship features

[![Latest Stable Version](https://img.shields.io/packagist/v/yiisoft/yii2-app-basic.svg)](https://packagist.org/packages/yiisoft/yii2-app-basic)
[![Total Downloads](https://img.shields.io/packagist/dt/yiisoft/yii2-app-basic.svg)](https://packagist.org/packages/yiisoft/yii2-app-basic)
[![build](https://github.com/yiisoft/yii2-app-basic/workflows/build/badge.svg)](https://github.com/yiisoft/yii2-app-basic/actions?query=workflow%3Abuild)

CONVERSATIONAL AI FEATURES
--------------------------

### Entity Extraction for Inventory Queries

The platform now includes intelligent entity extraction for natural language inventory queries. Users can ask about product availability using natural language, and the system automatically:

- **Detects Intent**: Identifies "check_inventory" intent from various query patterns
- **Extracts Entities**: Automatically identifies:
  - **Product Names**: "iPhone 15 Pro", "Samsung Galaxy S24" from natural descriptions
  - **SKUs**: Recognizes product SKU codes like "SAM24001", "IPH15PRO001"
- **Returns Formatted Results**: Provides detailed inventory information including quantity, pricing, and product details

**Example Queries:**
```
User: "check inventory of iPhone 15 Pro"
Bot: "Product 'iPhone 15 Pro' (SKU: IPH15PRO001) has 50 units in stock. Price: $999.00"

User: "how many Samsung Galaxy S24 do we have"
Bot: "Product 'Samsung Galaxy S24' (SKU: SAM24001) has 30 units in stock. Price: $799.00"

User: "check inventory of SAM24001"
Bot: "Product 'Samsung Galaxy S24' (SKU: SAM24001) has 30 units in stock. Price: $799.00"

User: "check inventory"
Bot: "Product name or SKU is required"
```

### Supported Intents

Currently supported automation intents:

1. **check_inventory**: Query product stock levels with entity extraction
2. **create_customer**: Create new customer accounts
3. **process_order**: Process orders from the shopping cart
4. **assign_license**: Assign product licenses
5. **generate_report**: Generate sales and analytics reports
6. **automate_workflow**: Automate custom business processes

DIRECTORY STRUCTURE
-------------------

      assets/             contains assets definition
      commands/           contains console commands (controllers)
      config/             contains application configurations
      controllers/        contains Web controller classes
      mail/               contains view files for e-mails
      migrations/         contains database migration files
      models/             contains model classes
      modules/
        chatbot/          Conversational AI module with NLP integration
      runtime/            contains files generated during runtime
      tests/              contains various tests for the application
      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources
      rasa/               Python environment for ChatterBot and NLP processing




REQUIREMENTS
------------

The minimum requirement by this project template that your Web server supports PHP 7.4.

**Python Integration for NLP:**
- Python 3.9 or higher for chatbot processing
- ChatterBot library for conversational AI
- SpaCy library with en_core_web_sm model for natural language processing
- Virtual environment setup for isolated Python dependencies


INSTALLATION
------------

### Install via Composer

If you do not have [Composer](https://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](https://getcomposer.org/doc/00-intro.md#installation-nix).

### ⚠️ IMPORTANT: Security Configuration

Before running this application, **you must** configure security settings:

1. **Copy the environment example file:**
   ```bash
   cp .env.example .env
   ```

2. **Edit `.env`** and change all default values, especially:
   - Database credentials (`DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME`)
   - Cookie validation key (`COOKIE_VALIDATION_KEY`) - generate a new one
   - Admin signup code (`ADMIN_SIGNUP_CODE`)
   - Email addresses (`ADMIN_EMAIL`, `SENDER_EMAIL`)

3. **See [SECURITY.md](SECURITY.md)** for detailed security setup instructions and best practices.

### Install from Composer

You can then install this project template using the following command:

~~~
composer create-project --prefer-dist yiisoft/yii2-app-basic basic
~~~

Now you should be able to access the application through the following URL, assuming `basic` is the directory
directly under the Web root.

~~~
http://localhost/basic/web/
~~~

### Install from an Archive File

Extract the archive file downloaded from [yiiframework.com](https://www.yiiframework.com/download/) to
a directory named `basic` that is directly under the Web root.

Set cookie validation key in `config/web.php` file to some random secret string:

```php
'request' => [
    // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
    'cookieValidationKey' => '<secret random string goes here>',
],
```

You can then access the application through the following URL:

~~~
http://localhost/basic/web/
~~~


### Install with Docker

Update your vendor packages

    docker-compose run --rm php composer update --prefer-dist
    
Run the installation triggers (creating cookie validation code)

    docker-compose run --rm php composer install    
    
Start the container

    docker-compose up -d
    
You can then access the application through the following URL:

    http://127.0.0.1:8000

**NOTES:** 
- Minimum required Docker engine version `17.04` for development (see [Performance tuning for volume mounts](https://docs.docker.com/docker-for-mac/osxfs-caching/))
- The default configuration uses a host-volume in your home directory `.docker-composer` for composer caches

### Python NLP Setup

After installation, set up the Python environment for chatbot functionality:

#### Option 1: Using requirements.txt (Recommended)

```bash
# Create and activate Python virtual environment
cd rasa
python3.9 -m venv rasa_env
source rasa_env/bin/activate  # On Windows: rasa_env\Scripts\activate

# Install all dependencies from requirements.txt
pip install -r requirements.txt

# Return to project root
cd ..
```

The `requirements.txt` file contains all necessary packages including:
- chatterbot==1.2.10 - Conversational AI engine
- spacy==3.8.0 - Natural language processing
- numpy, scipy - Scientific computing
- pydantic, pyyaml - Configuration management
- And 130+ additional dependencies

#### Option 2: Manual Installation

```bash
# Create and activate Python virtual environment
cd rasa
python3.9 -m venv rasa_env
source rasa_env/bin/activate  # On Windows: rasa_env\Scripts\activate

# Install required Python packages manually
pip install chatterbot chatterbot_corpus spacy
python -m spacy download en_core_web_sm

# Return to project root
cd ..
```

TESTING THE CHATBOT
-------------------

Run the entity extraction test to verify the conversational AI is working correctly:

```bash
php test_entity_extraction.php
```

Expected output shows successful entity extraction and inventory queries:
```
Testing: "check inventory of iPhone 15 Pro"
Result: Product 'iPhone 15 Pro' (SKU: IPH15PRO001) has 50 units in stock. Price: $999.00
Intent: check_inventory
Confidence: 0.8
```

ARCHITECTURE
------------

### Technology Stack

**Backend:**
- Yii 2 Framework (PHP 7.4+)
- MySQL/PostgreSQL database
- RESTful API endpoints

**Conversational AI:**
- ChatterBot - Intent detection and conversational responses
- SpaCy - Natural Language Processing
- Regex-based entity extraction - Product names and SKU identification
- Python 3.9 - NLP processing engine

### Module Architecture

**Chatbot Module** (`modules/chatbot/`)
- `services/ChatbotService.php` - Main orchestration service
- `services/NlpService.php` - Python chatbot interface
- `services/AutomationService.php` - Business logic execution
- `models/` - Database models for sessions and intents
- `controllers/` - API endpoints

### Data Flow

```
User Message
    ↓
[ChatbotService] - Receives and routes message
    ↓
[NlpService] - Executes Python chatbot
    ↓
[chatbot.py] - Intent detection + Entity extraction
    ↓
[ChatbotService] - Checks intent mapping
    ↓
[AutomationService] - Executes mapped automation
    ↓
Formatted Response to User
```



CONFIGURATION
-------------
