create table azienda(
id integer primary key auto_increment,
nome varchar(20) not null unique,
logo varchar(255) not null,
numProdotti integer default 0,
numInArrivo integer default 0,
numDisponibili integer default 0
)engine='InnoDB';

create table users(
id integer primary key auto_increment,
nome varchar(255) not null,
cognome varchar(255) not null,
username varchar(16) not null unique,
impiegato boolean not null,
impiego integer,
index ind_impiego(impiego),
foreign key(impiego) references azienda(id) on update cascade,
dataAssunzione timestamp NULL,
email varchar(255) not null unique,
password varchar(255) not null,
propic varchar(255) not null,
dataRegistrazione timestamp default current_timestamp,
spesaTot integer default 0,
numCarrello integer default 0,
stipendio integer default 0
)engine = 'InnoDB';

create table prodotto(
id integer primary key auto_increment,
titolo varchar(20) not null unique,
immagine varchar(255),
prezzo integer,
descrizione varchar(255),
disponibilita boolean,
inArrivo boolean,
produttore integer not null,
foreign key(produttore) references azienda(id) on update cascade,
index ind_produttore(produttore),
categoria varchar(20),
searchTitle varchar(20)
)engine='InnoDB';

create table suggerimenti(
id integer primary key auto_increment,
id_prodotto integer not null,
foreign key(id_prodotto) references prodotto(id) on update cascade on delete cascade,
index ind_id_prodotto(id_prodotto),
indexSuggerimento integer not null,
unique(id_prodotto, indexSuggerimento)
)engine='InnoDB';

create table recensioni(
id integer auto_increment primary key,
user integer not null,
foreign key(user) references users(id) on update cascade on delete cascade,
index ind_user(user),
prodotto integer not null,
foreign key(prodotto) references prodotto(id) on update cascade on delete cascade,
index ind_prodotto(prodotto),
descrizione varchar(255),
voto integer not null,
numLike integer default 0,
unique(user,prodotto),
data timestamp default current_timestamp
)engine='InnoDB';

create table like_recensioni(
id integer primary key auto_increment,
user integer not null,
foreign key(user) references users(id) on update cascade on delete cascade,
index ind_user(user),
recensione integer not null,
foreign key(recensione) references recensioni(id) on update cascade on delete cascade,
index ind_recensione(recensione),
unique(user,recensione)
)engine='InnoDB';


create table utente_prodotto(
id integer primary key auto_increment,
user integer not null,
foreign key(user) references users(id) on update cascade on delete cascade,
index ind_user(user),
prodotto integer not null,
foreign key(prodotto) references prodotto(id) on update cascade on delete cascade,
index ind_prodotto(prodotto),
wishlist boolean default false,
carrello integer default 0,
acquisto integer default 0,
unique(user,prodotto)
)engine='InnoDB';

create table impiegoPassato(
id integer primary key auto_increment,
user integer not null,
impiegoPassato integer not null,
dataAssunzione timestamp,
fineImpiego timestamp default current_timestamp,
foreign key(user) references users(id) on update cascade on delete cascade,
foreign key(impiegoPassato) references azienda(id) on update cascade,
index ind_user(user),
index ind_impiegoPassato(impiegoPassato)
)engine='InnoDB';

delimiter //
create trigger aggiorna_attributi_users
before update on utente_prodotto
for each row
begin
IF old.carrello<new.carrello THEN
update users set numCarrello=numCarrello+(new.carrello-old.carrello) where id=new.user;
update users set spesaTot=spesaTot+(select prezzo from prodotto where id=new.prodotto)*(new.carrello-old.carrello) where id=new.user;
ELSEIF old.carrello>new.carrello THEN
update users set numCarrello=numCarrello-(old.carrello-new.carrello) where id=new.user;
update users set spesaTot=spesaTot-(select prezzo from prodotto where id=new.prodotto)*(old.carrello-new.carrello) where id=new.user;
END IF;
IF new.carrello<0 THEN
signal sqlstate '45000' set message_text="Il carrello nnon può essere negativo";
END IF;
end //
delimiter ;

delimiter //
create trigger insert_attributi_users
before insert on utente_prodotto
for each row
begin
IF new.carrello>0 THEN
update users set numCarrello=numCarrello+(new.carrello) where id=new.user;
update users set spesaTot=spesaTot+(select prezzo from prodotto where id=new.prodotto)*new.carrello where id=new.user;
END IF;
end //
delimiter ;

delimiter //
create trigger aggiorna_aziende
before update on prodotto
for each row
begin
IF old.disponibilita = false and new.disponibilita = true THEN
update azienda set numDisponibili=numDisponibili+1 where id=new.produttore;
ELSEIF old.disponibilita = true and new.disponibilita = false THEN
update azienda set numDisponibili=numDisponibili-1 where id=new.produttore;
END IF;
IF old.inArrivo = false and new.inArrivo = true THEN
update azienda set numInArrivo=numInArrivo+1 where id=new.produttore;
ELSEIF old.inArrivo = true and new.inArrivo = false THEN
update azienda set numInArrivo=numInArrivo-1 where id=new.produttore;
END IF;
end //
delimiter ;

delimiter //
create trigger aggiorna_aziende_insert
before insert on prodotto
for each row
begin
update azienda set numProdotti=numProdotti+1 where id=new.produttore;
IF new.disponibilita = true THEN
update azienda set numDisponibili=numDisponibili+1 where id=new.produttore;
END IF;
IF new.inArrivo = true THEN
update azienda set numInArrivo=numInArrivo+1 where id=new.produttore;
END IF;
end //
delimiter ;

delimiter //
create trigger aggiorna_impiegoPassato
before update on users
for each row
begin
IF new.impiego<>old.impiego THEN
insert into impiegoPassato(user,impiegoPassato,dataAssunzione) values(new.id,old.impiego,old.dataAssunzione);
END IF;
end //
delimiter ;

delimiter //
create trigger aggiorna_like
before insert on like_recensioni
for each row
begin
update recensioni set numLike=numLike+1 where id=new.recensione;
end //
delimiter ;

delimiter //
create trigger aggiorna_like_delete
before delete on like_recensioni
for each row
begin
update recensioni set numLike=numLike-1 where id=old.recensione;
end //
delimiter ;



insert into azienda(nome,logo) values('Apple','apple.png');
insert into azienda(nome,logo) values('Microsoft','microsoft.jpg');
insert into azienda(nome,logo) values('Huawei','huawei.jpg');
insert into azienda(nome,logo) values('Xiaomi','xiaomi.png');
insert into azienda(nome,logo) values('Sony','sony.jpg');
insert into azienda(nome,logo) values('Samsung','samsung.png');


insert into prodotto(titolo,immagine,prezzo,descrizione,disponibilita,inArrivo,produttore,searchTitle,categoria) 
values('iPhone 12','iphone12.png',1189,'DISPLAY 6,1" 1170 x 2532 px. FOTOCAMERA 12 Mpx f/1.6. FRONTALE 12 Mpx f/2.2. CPU esa. RAM 4 GB. MEMORIA 64/128/256 GB. BATTERIA 2815 mAh. iOS 14.',true,false,1,'iphone12','Smartphone');
insert into prodotto(titolo,immagine,prezzo,descrizione,disponibilita,inArrivo,produttore,searchTitle,categoria) 
values('MacBook Air','macbookair.png',1159,'Chip Apple M1 con CPU 8‑core, GPU 7‑core e Neural Engine 16‑core. 8GB di memoria unificata. Unità SSD da 256GB. Display Retina con True Tone. Magic Keyboard retroilluminata - Italiano. Touch ID. Trackpad Force Touch. Due porte Thunderbolt/USB.',true,false,1,'macbookair','Portatili');
insert into prodotto(titolo,immagine,prezzo,descrizione,disponibilita,inArrivo,produttore,searchTitle,categoria) 
values('iPad','ipad.png',389,'DISPLAY 9,7" 1536 x 2048 px. FOTOCAMERA 8 mpx f/2.4. FRONTALE 1,2 mpx f/2.2. CPU Quad 2.34 GHz. RAM 2 GB. MEMORIA 32 GB. BATTERIA 7306 mAh. iOS.',false,true,1,'ipad','Tablet');
insert into prodotto(titolo,immagine,prezzo,descrizione,disponibilita,inArrivo,produttore,searchTitle,categoria) 
values('Apple Watch','applewatch.png',439,'Cassa da 44 mm o da 40 mm. Display Retina always-on. GPS + Cellular5. GPS. App Livelli O2. App ECG2. Notifiche in caso di frequenza cardiaca troppo alta o troppo bassa. Notifiche in caso di ritmo cardiaco irregolare. Resistente fino a 50m di profondità in acqua',true,false,1,'applewatch','Smartwatch');
insert into prodotto(titolo,immagine,prezzo,descrizione,disponibilita,inArrivo,produttore,searchTitle,categoria) 
values('AirPods','airpods.png',229,'Taglia unica. Chip H1. Funzione "Ehi Siri" sempre attiva. Fino a 5 ore di ascolto (con una sola carica. Più di 24 ore di ascolto (con la custodia di ricarica wireless). Custodia di ricarica wireless. Incisione personalizzata con iniziali, emoji e molto altro.',false,false,1,'airpods','Cuffie');
insert into prodotto(titolo,immagine,prezzo,descrizione,disponibilita,inArrivo,produttore,searchTitle,categoria) 
values('AirPods Max','airpodsmax.png',629,'PESO 384,8 g. DIMENSIONI 187,8 mm x 168,6 mm x 83,4 mm. SENSORI ottico, posizione, rilevamento custodia, accelerometro, giroscopio. BATTERIA Fino a 20 ore di ascolto. Ricarica tramite connettore Lightning.',true,false,1,null,'Cuffie');
insert into prodotto(titolo,immagine,prezzo,descrizione,disponibilita,inArrivo,produttore,searchTitle,categoria) 
values('Surface Pro 7','surface.png',919,'DIMENSIONI 292 mm x 201 mm x 8,5 mm. SCHERMO da 12,3" 2736x1824. MEMORIA 4 GB, 8 GB o 16 GB LPDDR4x. PROCESSORE Intel® Core™ i7-1065G7 Quad-Core di decima generazione. Windows 10 Home',true,false,2,null,'Portatili');
insert into prodotto(titolo,immagine,prezzo,descrizione,disponibilita,inArrivo,produttore,searchTitle,categoria) 
values('Xbox Series X','xbox.png',499,'CPU 8 core Zen 2, 3,8 GHz. GPU 12 TFLOPS, 52 CU a 1825 MHz. MEMORIA 16 GB GDDR6. SSD NVMe personalizzato da 1 TB. 4K a 60 FPS – fino a 120FPS',true,false,2,null,'Console');


insert into suggerimenti(id_prodotto,indexSuggerimento) values(1,13);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(1,14);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(1,15);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(1,16);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(1,17);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(1,18);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(1,19);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(1,20);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(2,48);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(2,49);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(2,50);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(2,51);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(2,52);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(3,0);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(3,4);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(3,14);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(3,29);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(3,30);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(4,9);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(4,10);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(4,11);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(4,12);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(4,13);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(5,65);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(5,66);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(5,67);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(5,68);
insert into suggerimenti(id_prodotto,indexSuggerimento) values(5,69);
