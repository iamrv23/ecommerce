<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'E-Commerce Platform',
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength' => 8,

    // E-commerce settings
    'ecommerce' => [
        'currency' => 'USD',
        'currency_symbol' => '$',
        'tax_rate' => 0.08, // 8% tax
        'free_shipping_threshold' => 100,
        'items_per_page' => 12,
        'max_cart_items' => 50,
        'order_number_prefix' => 'ORD-',
        'default_product_image' => '/images/no-image.png',
        'upload_path' => '@webroot/uploads',
        'upload_url' => '@web/uploads',
    ],

    // Chatbot settings
    'chatbot' => [
        'enabled' => true,
        'api_key' => 'your-openai-api-key-here',
        'model' => 'gpt-3.5-turbo',
        'max_tokens' => 150,
        'temperature' => 0.7,
        'system_prompt' => 'You are a helpful medical assistant chatbot specializing in typhoid fever. Provide accurate medical information, symptoms, prevention tips, and treatment advice. Always recommend consulting with healthcare professionals for proper diagnosis and treatment. Be empathetic and supportive.',
        'welcome_message' => 'Hello! I am your medical assistant chatbot. I can help you with information about typhoid fever, symptoms, prevention, and treatment. How can I assist you today?',
        'fallback_message' => 'I apologize, but I could not understand your question. Please try rephrasing or ask about typhoid symptoms, treatment, or prevention.',
        'session_timeout' => 1800, // 30 minutes
        'max_conversation_length' => 20,
    ],

    // Payment settings
    'payment' => [
        'stripe' => [
            'publishable_key' => 'your-stripe-publishable-key',
            'secret_key' => 'your-stripe-secret-key',
        ],
        'paypal' => [
            'client_id' => 'your-paypal-client-id',
            'client_secret' => 'your-paypal-client-secret',
            'sandbox' => true,
        ],
    ],

    // Email settings
    'email' => [
        'smtp' => [
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'username' => 'your-email@gmail.com',
            'password' => 'your-app-password',
            'encryption' => 'tls',
        ],
        'templates' => [
            'order_confirmation' => 'order-confirmation',
            'password_reset' => 'password-reset',
            'welcome' => 'welcome',
        ],
    ],

    // File upload settings
    'upload' => [
        'max_file_size' => 5 * 1024 * 1024, // 5MB
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'image_quality' => 85,
        'thumb_width' => 300,
        'thumb_height' => 300,
    ],

    // Cache settings
    'cache' => [
        'duration' => 3600, // 1 hour
        'product_cache_duration' => 7200, // 2 hours
        'category_cache_duration' => 86400, // 24 hours
    ],

    // Security settings
    'security' => [
        'enable_csrf' => true,
        'login_attempts' => 5,
        'lockout_duration' => 900, // 15 minutes
        'password_reset_token_expire' => 3600, // 1 hour
    ],
];
