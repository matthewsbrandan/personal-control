drop database if exists bd_pctrl;
	create database bd_pctrl;
    use bd_pctrl;
    create table usuario(
		id int(6) not null primary key auto_increment,
        nome varchar(30) not null,
		sobrenome varchar(30) not null,
		email varchar(30) unique not null,
		celular	varchar(12) not null
    );
	create table parcela(
		id int(6) not null primary key auto_increment,
        rotatividade enum('Diário','Semanal','Quinzenal','Mensal','Bimestral','Trimestral','Semestral','Anual'),
		quantidade int(2) not null,
		total decimal(14,2) default 0,
		usuario_id int(6) not null,
		foreign key (usuario_id) references usuario(id) 
    );
    create table conta(
		id int(6) not null primary key auto_increment,
        conta varchar(30) not null,
		saldo decimal(10,2) default 0,
		usuario_id int(6) not null,
        foreign key (usuario_id) references usuario(id)
    );
    create table categoria(
		id int(6) not null primary key auto_increment,
        nome varchar(30) not null,
		usuario_id int(6) not null,
        foreign key (usuario_id) references usuario(id)
    );
    create table caixa(
		id int(6) not null primary key auto_increment,
        mesano date not null,
		inicial	decimal(13,2) default 0,
		final decimal(13,2) default 0,
		inicial_parcial	decimal(13,2) default 0,
		final_parcial decimal(13,2) default 0,
		meta decimal(13,2) default 0,
		usuario_id int(6) not null,
        foreign key (usuario_id) references usuario(id)
    );
    create table movimento(
		id int(6) not null primary key auto_increment,
        tipo enum('Receita','Despesa') not null,
		data_ date not null,
		valor decimal(10,2) not null,
		descricao varchar(30) not null,
		obs varchar(150) null,
		relevancia enum('1','2','3','4','5'),
		status	bool default false,
		parcela	varchar(30) null,
		parcela_id int(6) null,
		conta_id int(6) null,
		categoria_id int(6) not null,
		caixa_id int(6) not null,
		usuario_id int(6) not null,
		foreign key (parcela_id) references parcela(id),
		foreign key (conta_id) references conta(id),
		foreign key (categoria_id) references categoria(id),
		foreign key (caixa_id) references caixa(id),
		foreign key (usuario_id) references usuario(id)
    );
    create table objetivo(
		id int(6) not null primary key auto_increment,
        nome varchar(30) not null,
		valor decimal(14,2) not null,
		relevancia enum('1','2','3','4','5') not null,
		status bool default false,
		usuario_id	int(6) not null,
		foreign key (usuario_id) references usuario(id)
    );