--#SQLITE#--
CREATE TABLE "cf_product" ( id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(100) NOT NULL, description VARCHAR(100), logo VARCHAR(100) , url_repository VARCHAR(300) NULL, login_repository VARCHAR(100) NULL, password_repository VARCHAR(100) NULL);
CREATE TABLE "cf_module" ( id INTEGER PRIMARY KEY AUTOINCREMENT, product_id INTEGER NOT NULL, name VARCHAR(100) NOT NULL, title VARCHAR(100) NOT NULL, description VARCHAR(100) NULL, imagem VARCHAR(100) );
CREATE TABLE "cf_group_menu" ( id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) NOT NULL );
CREATE TABLE "cf_feature" ( id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, module_id INTEGER(11) NOT NULL,  feature_id INTEGER(11) NULL, group_menu_id INTEGER(11) NULL, title VARCHAR(120) NULL, name VARCHAR(80) NULL, type VARCHAR(40) NULL, level INTEGER(11) );
CREATE TABLE "cf_feature_page" ( id integer PRIMARY KEY AUTOINCREMENT, feature_id integer, name varchar(100), controller varchar(100), CONSTRAINT feature_page_feature_id_fk FOREIGN KEY (feature_id) REFERENCES feature (id) );
--#SQLITE#--