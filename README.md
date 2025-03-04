# Note: This code is not complete, so it will not run

# Order Tracker Documentation

## Master Models Required

### Item Category
- **Fields**: 
  - `name`: The name of the item category.
  - `id`: The unique identifier for the item category.

### Stock Item
- **Fields**: 
  - `name`: The name of the stock item.
  - `id`: The unique identifier for the stock item.
  - `price`: The price of the stock item.
  - `category_id`: The identifier for the category to which the stock item belongs.

### Customer
- **Fields**: 
  - `name`: The name of the customer.
  - `mobile`: The mobile number of the customer.
  - `email`: The email address of the customer.
  - `address`: The physical address of the customer.

### User
- **Fields**: 
  - `name`: The name of the user.
  - `email`: The email address of the user.
  - `password`: The password for the user account.

### Order
- **Fields**: 
  - `order_date`: The date when the order was placed.
  - `customer_id`: The identifier for the customer who placed the order.
  - `reference`: A unique reference for the order.
  - `total`: The total amount for the order.
  - `bookkeeping_fields`: Additional fields for bookkeeping purposes.

### Order Items
- **Fields**: 
  - `order_id`: The identifier for the order to which the item belongs.
  - `item_id`: The identifier for the item.
  - `quantity`: The quantity of the item ordered.
  - `unit_price`: The unit price of the item.
  - `total`: The total price for the item (quantity * unit price).

## Unique Reference Generation

A basic code model is already included within these changes.

### ReferencePattern
- **Description**: A table that stores the pattern for the system type.

### MetaReference
- **Description**: This table holds the resolved patterns for any given context and their next sequence number.
- **Functionality**: 
  - Whenever an order is written, the `getNext` method is called in the MetaReference table with the persist option set to true.
  - Since the call is happening inside a transaction, no other transaction can modify the record concurrently, ensuring the reference is not duplicated.
  - Inside the `getNext` function, it parses the given context and creates a template. For example:
    - If the date is 2025-01-01 and the pattern is `ORD/{YY}/{SEQ:3}`, the MetaReference table stores each unique template.
    - It will have one entry for `ORD/25/{SEQ:3}` and one entry for `ORD/24/{SEQ:3}` for given two years, ensuring each unique pattern is only ever updated once concurrently.

## Workflow

### TaskType Table
- **Description**: Task description like large order above 1000, and a class which implements the interface for workflow resolution.

### Workflow Table
- **Description**: A master table for generating unique IDs.

### Workflow Definitions Table
- **Description**: Holds the assigned user for each task at any given stage like level one or level two.

### Workflow Interface
- **Methods**: 
  - `approve(TaskRecord $taskRecord): void`: Handles the approval of a task.
  - `reject(TaskRecord $taskRecord): void`: Handles the rejection of a task.

### Tasks & Task Transition Table
- **Description**: Stores all the levels in the approval workflow and their current status whether approved or rejected, by whom and when.
- **Functionality**: 
  - When the user logs in, we can check the tasks table for pending transitions where action is not taken, which is assigned to the logged-in user to show him a list of tasks assigned to him, and he can take action based on his authority.
  - Whenever an action is taken against a pending task, the task transition will happen, which will check if there are any additional levels in the approval hierarchy. If not, it calls the final approval method. If there is any additional level, then a new entry in the task transition table is added with the next assigned user.
  - This approach handles multiple levels of approval as well as encapsulates the workflow engine and abstracts away the process from each individual task that may come in the future.

## Order Processing

Order processing is straightforward. When the user hits the controller for storing an order, we check if the order total is greater than the large amount threshold and initiate the workflow.