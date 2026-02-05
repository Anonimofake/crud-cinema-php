-- DBCinema full schema + seed data (compatible with MySQL 5.7+)
-- Generated on 2026-02-05

DROP DATABASE IF EXISTS cinema;
CREATE DATABASE cinema CHARACTER SET utf8 COLLATE utf8_general_ci;
USE cinema;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS colonne_sonore;
DROP TABLE IF EXISTS recita_in;
DROP TABLE IF EXISTS ha_vinto;
DROP TABLE IF EXISTS film;
DROP TABLE IF EXISTS attori;
DROP TABLE IF EXISTS musicisti;
DROP TABLE IF EXISTS premi;
DROP TABLE IF EXISTS genere;

CREATE TABLE genere (
  id_genere INT(5) NOT NULL AUTO_INCREMENT,
  descrizione VARCHAR(30) DEFAULT NULL,
  PRIMARY KEY (id_genere)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE film (
  id_film INT(5) NOT NULL AUTO_INCREMENT,
  titolo VARCHAR(30) NOT NULL,
  anno INT(4) NOT NULL,
  regista VARCHAR(30) NOT NULL,
  nazionalita VARCHAR(30) DEFAULT NULL,
  produzione VARCHAR(30) DEFAULT NULL,
  distribuzione VARCHAR(30) DEFAULT NULL,
  durata TIME NOT NULL,
  colore TINYINT(1) DEFAULT NULL,
  trama VARCHAR(150) DEFAULT NULL,
  valutazione INT(2) DEFAULT NULL,
  id_genere INT(5) DEFAULT NULL,
  PRIMARY KEY (id_film),
  KEY idx_film_genere (id_genere),
  CONSTRAINT fk_film_genere FOREIGN KEY (id_genere) REFERENCES genere (id_genere)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE attori (
  id_attore INT(5) NOT NULL AUTO_INCREMENT,
  nominativo VARCHAR(20) NOT NULL,
  nazionalita VARCHAR(20) DEFAULT NULL,
  data_nascita DATE NOT NULL,
  sesso CHAR(1) NOT NULL,
  note VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (id_attore)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE musicisti (
  id_musicista INT(5) NOT NULL AUTO_INCREMENT,
  nominativo VARCHAR(20) NOT NULL,
  nazionalita VARCHAR(20) DEFAULT NULL,
  data_nascita DATE NOT NULL,
  sesso CHAR(1) NOT NULL,
  note VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (id_musicista)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE premi (
  id_premio INT(5) NOT NULL AUTO_INCREMENT,
  descrizione VARCHAR(30) DEFAULT NULL,
  manifestazione VARCHAR(20) DEFAULT NULL,
  PRIMARY KEY (id_premio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE colonne_sonore (
  id_musicista INT(5) NOT NULL,
  id_film INT(5) NOT NULL,
  brano VARCHAR(30) NOT NULL,
  valutazione INT(2) DEFAULT NULL,
  PRIMARY KEY (id_musicista, id_film),
  KEY idx_cs_film (id_film),
  CONSTRAINT fk_cs_musicista FOREIGN KEY (id_musicista) REFERENCES musicisti (id_musicista)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_cs_film FOREIGN KEY (id_film) REFERENCES film (id_film)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE recita_in (
  id_attore INT(5) NOT NULL,
  id_film INT(5) NOT NULL,
  personaggio VARCHAR(30) NOT NULL,
  valutazione INT(2) DEFAULT NULL,
  PRIMARY KEY (id_attore, id_film),
  KEY idx_ri_film (id_film),
  CONSTRAINT fk_ri_attore FOREIGN KEY (id_attore) REFERENCES attori (id_attore)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_ri_film FOREIGN KEY (id_film) REFERENCES film (id_film)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE ha_vinto (
  id_premio INT(5) DEFAULT NULL,
  id_film INT(5) DEFAULT NULL,
  anno INT(4) NOT NULL,
  KEY idx_hv_premio (id_premio),
  KEY idx_hv_film (id_film),
  CONSTRAINT fk_hv_premio FOREIGN KEY (id_premio) REFERENCES premi (id_premio)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_hv_film FOREIGN KEY (id_film) REFERENCES film (id_film)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO genere (id_genere, descrizione) VALUES
  (1,'Azione'),
  (2,'Commedia'),
  (3,'Avventura'),
  (4,'Giallo'),
  (5,'Fantasy');

INSERT INTO film (id_film, titolo, anno, regista, nazionalita, produzione, distribuzione, durata, colore, trama, valutazione, id_genere) VALUES
  (1,'Rambo',1998,'Ted Kotcheff','USA',NULL,NULL,'02:00:00',1,'Veterano in missione.',7,1),
  (2,'007',2012,'Sam Mendes','UK',NULL,NULL,'02:23:00',1,'James Bond torna in azione.',8,1),
  (3,'Avatar',2009,'James Cameron','USA',NULL,NULL,'02:42:00',1,'Mondo alieno e conflitti.',9,3),
  (4,'Interstellar',2014,'Christopher Nolan','USA',NULL,NULL,'02:49:00',1,'Viaggio nello spazio.',9,3),
  (5,'LaVitaeBella',1997,'Roberto Benigni','ITA',NULL,NULL,'01:56:00',1,'Storia di resilienza.',10,2),
  (6,'IlSignoreAnelli',2001,'Peter Jackson','NZ',NULL,NULL,'02:58:00',1,'Epic fantasy.',9,5);

INSERT INTO attori (id_attore, nominativo, nazionalita, data_nascita, sesso, note) VALUES
  (1,'Sylvester Stallone','USA','1946-07-06','M','Attore e regista'),
  (2,'Daniel Craig','UK','1968-03-02','M','Interprete di Bond'),
  (3,'Sam Worthington','AUS','1976-08-02','M','Protagonista di Avatar'),
  (4,'Matthew McConaughey','USA','1969-11-04','M','Interstellar'),
  (5,'Roberto Benigni','ITA','1952-10-27','M','Comico e regista'),
  (6,'Elijah Wood','USA','1981-01-28','M','Frodo Baggins');

INSERT INTO musicisti (id_musicista, nominativo, nazionalita, data_nascita, sesso, note) VALUES
  (1,'Hans Zimmer','GER','1957-09-12','M','Compositore'),
  (2,'James Horner','USA','1953-08-14','M','Avatar OST'),
  (3,'Thomas Newman','USA','1955-10-20','M','007 Skyfall'),
  (4,'Howard Shore','CAN','1946-10-18','M','LOTR OST');

INSERT INTO premi (id_premio, descrizione, manifestazione) VALUES
  (1,'Miglior Film','Oscar'),
  (2,'Miglior Regia','Oscar'),
  (3,'Miglior Colonna','Oscar'),
  (4,'Premio Speciale','Cannes');

INSERT INTO colonne_sonore (id_musicista, id_film, brano, valutazione) VALUES
  (1,4,'Cornfield Chase',10),
  (2,3,'Main Theme',9),
  (3,2,'Skyfall Theme',8),
  (4,6,'The Shire',9);

INSERT INTO recita_in (id_attore, id_film, personaggio, valutazione) VALUES
  (1,1,'John Rambo',9),
  (2,2,'James Bond',9),
  (3,3,'Jake Sully',8),
  (4,4,'Cooper',9),
  (5,5,'Guido',10),
  (6,6,'Frodo',9);

INSERT INTO ha_vinto (id_premio, id_film, anno) VALUES
  (1,5,1999),
  (2,4,2015),
  (3,4,2015),
  (4,3,2009);

SET FOREIGN_KEY_CHECKS = 1;

ALTER TABLE genere AUTO_INCREMENT = 6;
ALTER TABLE film AUTO_INCREMENT = 7;
ALTER TABLE attori AUTO_INCREMENT = 7;
ALTER TABLE musicisti AUTO_INCREMENT = 5;
ALTER TABLE premi AUTO_INCREMENT = 5;
