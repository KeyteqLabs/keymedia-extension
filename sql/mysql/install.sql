CREATE TABLE ezr_keymedia_backends (
    id int(11) NOT NULL AUTO_INCREMENT,
    host varchar(255) NOT NULL,
    username varchar(255) NOT NULL,
    api_key varchar(255) NOT NULL,
    api_version int(2) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
