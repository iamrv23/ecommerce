#!/usr/bin/env python3
"""
Simple Chatbot using ChatterBot for e-commerce automation
"""

import json
import sys
import os
import logging

# Suppress chatterbot training logs
logging.getLogger('chatterbot').setLevel(logging.ERROR)

from chatterbot import ChatBot
from chatterbot.trainers import ChatterBotCorpusTrainer, ListTrainer

class EcommerceChatbot:
    def __init__(self):
        self.chatbot = ChatBot(
            'EcommerceBot',
            storage_adapter='chatterbot.storage.SQLStorageAdapter',
            database_uri='sqlite:///chatbot.db',
            logic_adapters=[
                {
                    'import_path': 'chatterbot.logic.BestMatch',
                    'default_response': 'I am sorry, but I do not understand.',
                    'maximum_similarity_threshold': 0.90
                }
            ]
        )

        # Train with e-commerce specific conversations
        self._train_ecommerce_data()

    def _train_ecommerce_data(self):
        trainer = ListTrainer(self.chatbot)

        # Customer creation intents
        trainer.train([
            "I want to create a new customer account",
            "create_customer",
            "I need to add a new customer",
            "create_customer",
            "register a new customer",
            "create_customer"
        ])

        # Order processing intents
        trainer.train([
            "I want to place an order",
            "process_order",
            "create a new order",
            "process_order",
            "I need to process an order",
            "process_order"
        ])

        # Inventory check intents with product examples
        trainer.train([
            "check inventory",
            "check_inventory",
            "what's in stock",
            "check_inventory",
            "inventory status",
            "check_inventory",
            "check inventory of iPhone 15 Pro",
            "check_inventory",
            "how many iPhone 15 Pro do we have",
            "check_inventory",
            "inventory for Samsung Galaxy S24",
            "check_inventory",
            "check stock for product SKU-123",
            "check_inventory"
        ])

        # License assignment intents
        trainer.train([
            "assign license",
            "assign_license",
            "I need to assign a license",
            "assign_license",
            "license assignment",
            "assign_license"
        ])

        # Report queries
        trainer.train([
            "show me reports",
            "generate_report",
            "I need a report",
            "generate_report",
            "sales report",
            "generate_report",
            "generate sales report",
            "generate_report"
        ])

        # Workflow automation
        trainer.train([
            "automate workflow",
            "automate_workflow",
            "I need to automate a process",
            "automate_workflow",
            "workflow automation",
            "automate_workflow"
        ])

    def get_response(self, message):
        """Get chatbot response and detect intent"""
        response = self.chatbot.get_response(message)

        # Map responses to intents
        intent_mapping = {
            "create_customer": ["create", "customer", "account", "register", "new"],
            "process_order": ["order", "place", "process", "purchase"],
            "check_inventory": ["inventory", "stock", "check", "available", "how many"],
            "assign_license": ["license", "assign", "key", "activation"],
            "generate_report": ["report", "sales", "analytics", "data"],
            "automate_workflow": ["automate", "workflow", "process", "task"]
        }

        detected_intent = None
        confidence = 0.0
        entities = []

        message_lower = message.lower()
        for intent, keywords in intent_mapping.items():
            if any(keyword in message_lower for keyword in keywords):
                detected_intent = intent
                confidence = 0.8  # Simple keyword matching confidence
                break

        # Extract entities based on intent
        if detected_intent == "check_inventory":
            entities = self._extract_inventory_entities(message)
        elif detected_intent == "generate_report":
            entities = self._extract_report_entities(message)
        # Add more entity extraction for other intents as needed

        return {
            'intent': detected_intent,
            'confidence': confidence,
            'entities': entities,
            'response': str(response),
            'text': message
        }

    def _extract_inventory_entities(self, message):
        """Extract product name or SKU from inventory check messages"""
        entities = []
        import re

        # First, try to extract complete product names using regex patterns
        product_patterns = [
            r'check inventory of (.+)',
            r'inventory for (.+)',
            r'how many (.+) do we have',
            r'stock for (.+)',
            r'check (.+) inventory',
        ]

        for pattern in product_patterns:
            match = re.search(pattern, message, re.IGNORECASE)
            if match:
                product_text = match.group(1).strip()
                # Clean up the product text
                product_text = re.sub(r'^(the|a|an)\s+', '', product_text, flags=re.IGNORECASE)

                # Skip if it contains command words or is too short
                if (len(product_text) < 3 or
                    re.match(r'^(do we have|is in stock|are available|check|inventory|stock)$', product_text, re.IGNORECASE)):
                    continue

                # Check if this looks like a SKU instead of a product name
                if re.match(r'^([A-Z0-9]+[-_][A-Z0-9]+|[A-Z]+[0-9]+[A-Z0-9]*|[0-9]+[A-Z]+[A-Z0-9]*)$', product_text) and len(product_text) >= 4:
                    entities.append({
                        'entity': 'sku',
                        'value': product_text.upper(),
                        'start': match.start(1),
                        'end': match.end(1)
                    })
                else:
                    entities.append({
                        'entity': 'product_name',
                        'value': product_text,
                        'start': match.start(1),
                        'end': match.end(1)
                    })
                return entities  # Return immediately if we find a product name or SKU

        # If no product name found, look for SKU patterns
        # More restrictive SKU pattern: requires mix of letters and numbers, or dashes
        sku_pattern = r'\b([A-Z0-9]+[-_][A-Z0-9]+|[A-Z]+[0-9]+[A-Z0-9]*|[0-9]+[A-Z]+[A-Z0-9]*)\b'
        sku_matches = re.findall(sku_pattern, message)

        for sku in sku_matches:
            # Skip if it's a common word or too short
            if (len(sku) < 4 or
                re.match(r'^(the|and|for|are|you|how|what|check|inventory|stock|product|s24)$', sku, re.IGNORECASE)):
                continue

            # Look for context around the SKU
            sku_start = message.upper().find(sku.upper())
            if sku_start >= 0:
                entities.append({
                    'entity': 'sku',
                    'value': sku.upper(),
                    'start': sku_start,
                    'end': sku_start + len(sku)
                })
                return entities  # Return immediately if we find a SKU

        return entities

    def _extract_report_entities(self, message):
        """Extract report type from report generation messages"""
        entities = []

        # Look for report types
        report_types = ['sales', 'inventory', 'customer', 'order', 'revenue', 'profit']
        message_lower = message.lower()

        for report_type in report_types:
            if report_type in message_lower:
                entities.append({
                    'entity': 'report_type',
                    'value': report_type,
                    'start': message_lower.find(report_type),
                    'end': message_lower.find(report_type) + len(report_type)
                })
                break

        return entities

def main():
    if len(sys.argv) < 2:
        print(json.dumps({'error': 'No message provided'}))
        sys.exit(1)

    message = sys.argv[1]
    sender = sys.argv[2] if len(sys.argv) > 2 else None

    try:
        # Suppress all output except our final JSON
        old_stdout = sys.stdout
        old_stderr = sys.stderr
        sys.stdout = open(os.devnull, 'w')
        sys.stderr = open(os.devnull, 'w')
        
        chatbot = EcommerceChatbot()
        result = chatbot.get_response(message)
        
        # Restore stdout and print result
        sys.stdout = old_stdout
        sys.stderr = old_stderr
        print(json.dumps(result))
    except Exception as e:
        # Restore stdout/stderr and print error
        sys.stdout = old_stdout
        sys.stderr = old_stderr
        print(json.dumps({
            'error': str(e),
            'intent': None,
            'confidence': 0.0,
            'response': 'Sorry, I encountered an error processing your request.',
            'text': message
        }))

if __name__ == '__main__':
    main()