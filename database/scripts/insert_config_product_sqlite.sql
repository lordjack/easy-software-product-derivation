INSERT INTO cf_product (id,name,description,url_repository,login_repository,password_repository) VALUES ('2', 'bloglps', 'BLOG LPS','https://lordjackson@bitbucket.org/jacklan/bloglps.git','lordjackson@gmail.com', '');
INSERT INTO cf_group_menu (name) VALUES ('CADASTRO');
INSERT INTO cf_group_menu (name) VALUES ('RELATÓRIO');
INSERT INTO cf_group_menu (name) VALUES ('GRÁFICO');
INSERT INTO cf_group_menu (name) VALUES ('ACOMPANHAMENTO');
INSERT INTO cf_module (product_id,name,title) VALUES ('2','ADMIN','ADMINISTRADOR');
INSERT INTO cf_feature (module_id,title,name,type,group_menu_id) VALUES ('3','Post*','post','MANDATORY','1');
INSERT INTO cf_feature_page (feature_id,name,controller) VALUES ('1','Post Form','PostForm');
INSERT INTO cf_feature_page (feature_id,name,controller) VALUES ('1','Post List','PostList');
INSERT INTO cf_feature_page (feature_id,name,controller) VALUES ('1','Post Render','PostRender');
INSERT INTO cf_feature (module_id,title,name,type,group_menu_id) VALUES ('3','Category*','category','MANDATORY','1');
INSERT INTO cf_feature_page (feature_id,name,controller) VALUES ('2','Category','CategoryFormList');
INSERT INTO cf_feature_page (feature_id,name,controller) VALUES ('2','Category Render','CategoryRender');
INSERT INTO cf_feature (module_id,title,name,type,group_menu_id) VALUES ('3','Content*','content','MANDATORY','1');
INSERT INTO cf_feature_page (feature_id,name,controller) VALUES ('3','Content Form','ContentForm');
INSERT INTO cf_feature_page (feature_id,name,controller) VALUES ('3','Login','LoginForm');
