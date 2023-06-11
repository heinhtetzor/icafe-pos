# set null to unlinked waiters
UPDATE icafe_pk.orders 
SET waiter_id = NULL
WHERE waiter_id NOT IN (SELECT id from icafe_pk.waiters);

UPDATE icafe_pk.order_menus 
SET waiter_id = 1
WHERE waiter_id NOT IN (SELECT id from icafe_pk.waiters);

#manual add table id with 1
INSERT INTO icafe_pk.tables (id, store_id, name, created_at, updated_at, table_group_id)
VALUES 
(1, 4, 'Table 1', now(), now(), 2)

#icafe pk updates

SELECT @max := MAX(id) + 20 FROM icafe.expenses;
UPDATE icafe_pk.expenses
SET id = @max + id;


SELECT @max := MAX(id) + 20 FROM icafe.expense_items;
UPDATE icafe_pk.expense_items
SET id = @max + id;


SELECT @max := MAX(id) + 20 FROM icafe.expense_stock_menus;
UPDATE icafe_pk.expense_stock_menus
SET id = @max + id;

SELECT @max := MAX(id) + 20 FROM icafe.items;
UPDATE icafe_pk.items
SET id = @max + id;

SELECT @max := MAX(id) + 20 FROM icafe.kitchens;
UPDATE icafe_pk.kitchens
SET id = @max + id;

SELECT @max := MAX(id) + 20 FROM icafe.tables;
UPDATE icafe_pk.tables
SET id = @max + id;

SELECT @max := MAX(id) + 20 FROM icafe.menu_groups;
UPDATE icafe_pk.menu_groups
SET id = @max + id;

SELECT @max := MAX(id) + 20 FROM icafe.menus;
UPDATE icafe_pk.menus
SET id = @max + id;

SELECT @max := MAX(id) + 20 FROM icafe.menu_group_kitchens;
UPDATE icafe_pk.menu_group_kitchens
SET id = @max + id;

SELECT @max := MAX(id) + 20 FROM icafe.orders;
UPDATE icafe_pk.orders
SET id = @max + id;

SELECT @max := MAX(id) + 10000 FROM icafe.order_menus;
UPDATE icafe_pk.order_menus
SET id = @max + id;

SELECT @max := MAX(id) + 20 FROM icafe.settings;
UPDATE icafe_pk.settings
SET id = @max + id;

SELECT @max := MAX(id) + 20 FROM icafe.stock_menus;
UPDATE icafe_pk.stock_menus
SET id = @max + id;

SELECT @max := MAX(id) + 20 FROM icafe.stock_menu_entries;
UPDATE icafe_pk.stock_menu_entries
SET id = @max + id;

SELECT @max := MAX(id) + 20 FROM icafe.table_groups;
UPDATE icafe_pk.table_groups
SET id = @max + id;

SELECT @max := MAX(id) + 20 FROM icafe.table_statuses;
UPDATE icafe_pk.tables
SET id = @max + id;

SELECT @max := MAX(id) + 20 FROM icafe.table_statuses;
UPDATE icafe_pk.table_statuses
SET id = @max + id;

SELECT @max := MAX(id) + 20 FROM icafe.waiters;
UPDATE icafe_pk.waiters
SET id = @max + id;



mysqldump --no-create-info -u root -p icafe_pk expenses expense_items expense_stock_menus items kitchens tables menu_groups menus menu_group_kitchens orders order_menus settings stock_menus stock_menu_entries table_groups table_statuses waiters > ~/dumps/icafepk-offsetted.sql
