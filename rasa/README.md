# Rasa Configuration Files

## Setup Instructions

1. Install Rasa:
```bash
pip install rasa
```

2. Initialize Rasa project in the `rasa/` directory:
```bash
cd rasa
rasa init
```

3. Replace the generated files with the configurations below.

4. Train the model:
```bash
rasa train
```

5. Start Rasa server:
```bash
rasa run --cors "*" --port 5005
```

6. Start action server (if using custom actions):
```bash
rasa run actions
```

## Files to create:

### nlu.yml
```yaml
version: "3.1"

nlu:
- intent: create_customer
  examples: |
    - I want to create a new customer account
    - Can you help me register a new user?
    - Add a customer with name [John Doe](first_name) [Smith](last_name)
    - Create customer account for [jane@example.com](email)

- intent: process_order
  examples: |
    - I want to place an order
    - Can you process my order?
    - Buy [laptop](product_name) for me
    - Order [5](quantity) [phones](product_name)

- intent: check_inventory
  examples: |
    - Do you have [laptop](product_name) in stock?
    - Check inventory for [SKU123](sku)
    - How many [phones](product_name) do you have?
    - Is [tablet](product_name) available?

- intent: get_order_status
  examples: |
    - What's the status of order [12345](order_id)?
    - Check my order status
    - Where is my order [ORD-12345](order_id)?

- intent: assign_license
  examples: |
    - Assign a license to [user123](user_id)
    - Give me a license key
    - I need a software license

- intent: generate_report
  examples: |
    - Generate sales report
    - Show me the monthly report
    - Create inventory report

- intent: greet
  examples: |
    - hey
    - hello
    - hi
    - good morning
    - good evening

- intent: goodbye
  examples: |
    - bye
    - goodbye
    - see you later
    - have a good day
```

### domain.yml
```yaml
version: "3.1"

intents:
  - create_customer
  - process_order
  - check_inventory
  - get_order_status
  - assign_license
  - generate_report
  - greet
  - goodbye

entities:
  - first_name
  - last_name
  - email
  - product_name
  - sku
  - quantity
  - order_id
  - user_id

responses:
  utter_greet:
    - text: "Hello! How can I help you today?"

  utter_goodbye:
    - text: "Goodbye! Have a great day!"

  utter_default:
    - text: "I'm sorry, I didn't understand that. Can you please rephrase?"

session_config:
  session_expiration_time: 60
  carry_over_slots_to_new_session: true
```

### stories.yml
```yaml
version: "3.1"

stories:
- story: greet and goodbye
  steps:
  - intent: greet
  - action: utter_greet
  - intent: goodbye
  - action: utter_goodbye

- story: create customer
  steps:
  - intent: create_customer
  - action: action_create_customer
  - action: utter_customer_created

- story: process order
  steps:
  - intent: process_order
  - action: action_process_order
  - action: utter_order_processed

- story: check inventory
  steps:
  - intent: check_inventory
  - action: action_check_inventory
  - action: utter_inventory_status

- story: get order status
  steps:
  - intent: get_order_status
  - action: action_get_order_status
  - action: utter_order_status

- story: assign license
  steps:
  - intent: assign_license
  - action: action_assign_license
  - action: utter_license_assigned

- story: generate report
  steps:
  - intent: generate_report
  - action: action_generate_report
  - action: utter_report_generated
```

### actions.py (if using custom actions)
```python
from typing import Any, Text, Dict, List
from rasa_sdk import Action, Tracker
from rasa_sdk.executor import CollectingDispatcher
from rasa_sdk.events import SlotSet

class ActionCreateCustomer(Action):
    def name(self) -> Text:
        return "action_create_customer"

    def run(self, dispatcher: CollectingDispatcher,
            tracker: Tracker,
            domain: Dict[Text, Any]) -> List[Dict[Text, Any]]:

        # This would call your Yii2 API
        # For now, just return a message
        dispatcher.utter_message(text="Customer created successfully!")
        return []

# Add other action classes...
```

### endpoints.yml
```yaml
action_endpoint:
  url: "http://localhost:5055/webhook"

# If using custom actions, uncomment and configure
# action_endpoint:
#   url: "http://localhost:5055/webhook"
```