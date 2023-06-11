USE icafe;

SET @store_id = 1; 

UPDATE `settings` SET store_id = @store_id WHERE store_id IS NULL;
UPDATE `table_groups` SET store_id = @store_id WHERE store_id IS NULL;
UPDATE `tables` SET store_id = @store_id WHERE store_id IS NULL and name != 'Express';
UPDATE `items` SET store_id = @store_id WHERE store_id IS NULL;
UPDATE `menu_groups` SET store_id = @store_id WHERE store_id IS NULL;
UPDATE `menus` SET store_id = @store_id WHERE store_id IS NULL;
UPDATE `kitchens` SET store_id = @store_id WHERE store_id IS NULL;
UPDATE `expenses` SET store_id = @store_id WHERE store_id IS NULL;
UPDATE `orders` SET store_id = @store_id WHERE store_id IS NULL;
UPDATE `waiters` SET store_id = @store_id WHERE store_id IS NULL;
