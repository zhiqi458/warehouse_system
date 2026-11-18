-- MySQL Workbench setup: execute this file to create/select the application schema.
CREATE DATABASE IF NOT EXISTS warehouse_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE warehouse_management;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  full_name VARCHAR(120) NOT NULL,
  username VARCHAR(50) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id), UNIQUE KEY uq_users_username (username), KEY idx_users_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS items (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  item_code VARCHAR(40) NOT NULL,
  item_name VARCHAR(160) NOT NULL,
  category VARCHAR(100) NOT NULL,
  location VARCHAR(80) NOT NULL,
  quantity INT UNSIGNED NOT NULL DEFAULT 0,
  unit VARCHAR(40) NOT NULL,
  reorder_level INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id), UNIQUE KEY uq_items_code (item_code), KEY idx_items_category (category), KEY idx_items_name (item_name), KEY idx_items_low_stock (quantity,reorder_level)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS stock_movements (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  item_id INT UNSIGNED NOT NULL,
  movement_type ENUM('IN','OUT') NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  movement_date DATE NOT NULL,
  reference_note VARCHAR(255) NULL,
  created_by INT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_movements_item_date (item_id,movement_date),
  KEY idx_movements_type_date (movement_type,movement_date),
  KEY idx_movements_created_by (created_by),
  CONSTRAINT fk_movements_item FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_movements_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Demo accounts: admin / admin123 and demo / user123
INSERT IGNORE INTO users (id,full_name,username,password_hash,role) VALUES
(1,'Warehouse Admin','admin','$2y$10$fdwd2X1JNx6MkYOkvjNpgOyi5u8EUNb6bwCM6k9luB4pdeuDr1LnO','admin'),
(2,'Demo User','demo','$2y$10$8J0icbek4W7Xpd2dG76hl.z1nhEhXga6Hsec30Tfyra5iWrzuYXAO','user');

INSERT IGNORE INTO items (id,item_code,item_name,category,location,quantity,unit,reorder_level) VALUES
(1,'ITM-001','Industrial Gloves','Safety','A1-01',120,'pairs',30),
(2,'ITM-002','Packing Tape','Packaging','B2-04',85,'rolls',20),
(3,'ITM-003','Cardboard Box M','Packaging','B1-02',40,'pcs',50),
(4,'ITM-004','LED Work Light','Electrical','C3-07',15,'pcs',10),
(5,'ITM-005','Hydraulic Oil 5L','Maintenance','D1-03',22,'bottles',15);

INSERT INTO stock_movements (item_id,movement_type,quantity,movement_date,reference_note,created_by)
SELECT * FROM (SELECT 1 AS item_id,'IN' AS movement_type,40 AS quantity,CURDATE() - INTERVAL 6 DAY AS movement_date,'PO-1042' AS reference_note,1 AS created_by UNION ALL
SELECT 2,'OUT',12,CURDATE() - INTERVAL 5 DAY,'Packing line replenishment',1 UNION ALL
SELECT 3,'OUT',15,CURDATE() - INTERVAL 3 DAY,'Dispatch order SO-288',1 UNION ALL
SELECT 4,'IN',8,CURDATE() - INTERVAL 2 DAY,'PO-1048',1 UNION ALL
SELECT 5,'OUT',3,CURDATE() - INTERVAL 1 DAY,'Maintenance request MR-19',1 UNION ALL
SELECT 1,'OUT',10,CURDATE(),'Shift issue note SI-64',1) AS demo
WHERE NOT EXISTS (SELECT 1 FROM stock_movements LIMIT 1);
