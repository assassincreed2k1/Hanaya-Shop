<?php

return [
    // Logo image path
    'logo_path' => 'fixed_resources/logo.png',

    // Favicon path
    'favicon_path' => 'favicon.ico',

    // Fixed resources folder path
    'fixed_resources_path' => 'fixed_resources/',

    // Shop information (can be used for footer, contact...)
    'shop_name' => 'HANAYA SHOP',
    'shop_email' => 'assassincreed2k1@gmail.com',
    'shop_phone' => '0948512463',
    'shop_address' => '123 Flower Street, Son Tay, Hanoi',

    // Banner configuration
    'banners' => [
        [
            'image' => 'fixed_resources/banner_1.jpg',
            'title' => 'Welcome to Hanaya Shop',
            'subtitle' => 'Where meaningful flowers and gifts come together',
            'button_text' => 'Explore Now',
            'button_link' => '/products'
        ],
        [
            'image' => 'fixed_resources/banner_2.jpg',
            'title' => 'Soap Flower Collection',
            'subtitle' => 'Eternal flowers with gentle fragrance',
            'button_text' => 'View Collection',
            'button_link' => '/products?category_name=soap-flower'
        ],
        [
            'image' => 'fixed_resources/banner_3.jpg',
            'title' => 'Special Gifts',
            'subtitle' => 'Meaningful gifts for your loved ones',
            'button_text' => 'Find Gifts',
            'button_link' => '/products?category_name=souvenir'
        ]
    ],

    //Checkout
    'checkout' => [
        'shipping_fee' => 8, // Fixed shipping fee
    ],

    // Order status configuration  
    'order_statuses' => [
        'pending' => 'pending',
        'processing' => 'processing', 
        'shipped' => 'shipped',
        'completed' => 'completed',
        'cancelled' => 'cancelled',
    ],

    // Review configuration
    'review' => [
        'can_review_status' => 'completed', // Status when user can review
        'max_rating' => 5,
        'min_rating' => 1,
        'default_rating' => 5,
    ],

    // Chatbot messages
    'chatbot_greeting' =>
    "Hello! I'm Hanaya Shop's chatbot. I can help you with:\n"
        . "🌸 Product search\n"
        . "📝 Order information\n"
        . "📋 Product categories\n"
        . "📰 Latest news\n"
        . "❓ Frequently asked questions\n\n"
        . "How can I help you?",
    'chatbot_help' =>
    "🤖 Hanaya Shop chatbot user guide:\n\n"
        . "🔍 Find products:\n"
        . "• 'find soap flowers'\n"
        . "• 'gift products'\n\n"
        . "📂 View categories:\n"
        . "• 'product categories'\n"
        . "• 'product types'\n\n"
        . "📦 Check orders:\n"
        . "• 'my orders'\n"
        . "• 'order status'\n\n"
        . "📰 News:\n"
        . "• 'latest news'\n"
        . "• 'articles'\n\n"
        . "🏪 Store information:\n"
        . "• 'store info'\n"
        . "• 'contact address'\n\n"
        . "Feel free to ask me anything!",
    'chatbot_store_info' =>
    "🏪 Hanaya Shop store information:\n\n"
        . "📍 Address: 123 Flower Street, Son Tay, Hanoi\n"
        . "📞 Phone: 0948512463\n"
        . "📧 Email: assassincreed2k1@gmail.com\n"
        . "🕒 Opening hours: 8:00 - 22:00 (Monday - Sunday)\n\n"
        . "🚚 Delivery: Nationwide\n"
        . "💳 Payment: Cash, bank transfer, cards\n\n"
        . "Contact us now for consultation!",
    'chatbot_status' => [
        'pending' => "Pending",
        'processing' => "Processing",
        'completed' => "Completed",
        'cancelled' => "Cancelled"
    ],
    'chatbot_price_info' =>
    "About our product prices:\n\n"
        . "🌸 Soap flowers: From 15 USD - 500 USD\n"
        . "🎁 Souvenirs: From 5 USD - 800 USD\n\n"
        . "Specific prices depend on size, materials, and design.\n"
        . "See detailed prices at: /products",
    'chatbot_default' =>
    "Sorry, I don't understand your question. You can ask me about:\n"
        . "• Products (e.g., 'find soap flowers')\n"
        . "• Product categories\n"
        . "• Your orders\n"
        . "• Latest news\n"
        . "• Store information\n\n"
        . "Or type 'help' for detailed instructions.",
        
];
