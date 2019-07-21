--#SQLITE#--
/*
CREATE TABLE "product" ( id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(100) NOT NULL, description VARCHAR(100), logo VARCHAR(100) , url_repository VARCHAR(300) NULL, login_repository VARCHAR(100) NULL, password_repository VARCHAR(100) NULL);
CREATE TABLE "module" ( id INTEGER PRIMARY KEY AUTOINCREMENT, product_id INTEGER NOT NULL, name VARCHAR(100) NOT NULL, title VARCHAR(100) NOT NULL, description VARCHAR(100) NOT NULL, imagem VARCHAR(100) );
CREATE TABLE "group_menu" ( id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) NOT NULL );
CREATE TABLE "feature" ( id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, module_id INTEGER(11) NOT NULL, title VARCHAR(120) NOT NULL, name VARCHAR(80) NOT NULL, type VARCHAR(40) NOT NULL, level INTEGER(11), feature_id INTEGER(11), group_menu_id INTEGER );
CREATE TABLE "feature_page" ( id integer PRIMARY KEY AUTOINCREMENT, feature_id integer, name varchar(100), controller varchar(100), CONSTRAINT feature_page_feature_id_fk FOREIGN KEY (feature_id) REFERENCES feature (id) );
*/
--#POSTGRES#--
CREATE TABLE public.cf_product (
  id BIGSERIAL NOT NULL,
  name VARCHAR(100) NOT NULL,
  description VARCHAR(100),
  logo VARCHAR(100),
  url_repository VARCHAR(300),
  login_repository VARCHAR(100),
  password_repository VARCHAR(50),
  PRIMARY KEY(id)
)
WITH (oids = false);


CREATE TABLE public.cf_module (
  id BIGSERIAL,
  product_id BIGINT NOT NULL,
  name VARCHAR(100) NOT NULL,
  title VARCHAR(100),
  description VARCHAR(100),
  imagem VARCHAR(100),
  CONSTRAINT cf_module_pkey PRIMARY KEY(id),
  CONSTRAINT cf_module_fk FOREIGN KEY (product_id)
    REFERENCES public.cf_product(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE
)
WITH (oids = false);

CREATE TABLE public.cf_group_menu (
  id BIGSERIAL NOT NULL,
  name VARCHAR(100),
  PRIMARY KEY(id)
)
WITH (oids = false);

CREATE TABLE public.cf_feature (
  id BIGSERIAL,
  module_id BIGINT NOT NULL,
  feature_id BIGINT,
  group_menu_id BIGINT,
  title VARCHAR(100),
  name VARCHAR(100),
  type VARCHAR(40),
  level BIGINT,
  CONSTRAINT cf_feature_pkey PRIMARY KEY(id),
  CONSTRAINT cf_feature_fk FOREIGN KEY (feature_id)
    REFERENCES public.cf_feature(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT cf_group_menu_fk1 FOREIGN KEY (group_menu_id)
    REFERENCES public.cf_group_menu(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT cf_module_fk FOREIGN KEY (module_id)
    REFERENCES public.cf_module(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE
)
WITH (oids = false);


CREATE TABLE public.cf_feature_page (
  id BIGSERIAL NOT NULL,
  feature_id BIGINT,
  name VARCHAR(100),
  controller VARCHAR(100),
  level BIGINT,
  PRIMARY KEY(id)
)
WITH (oids = false);

CREATE TABLE public.cf_feature_page (
  id BIGSERIAL,
  feature_id BIGINT,
  name VARCHAR(100),
  controller VARCHAR(100),
  level BIGINT,
  CONSTRAINT cf_feature_page_pkey PRIMARY KEY(id),
  CONSTRAINT cf_feature_page_fk FOREIGN KEY (feature_id)
    REFERENCES public.cf_feature(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE
)
WITH (oids = false);