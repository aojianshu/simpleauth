# simpleauth

Demonstration for Simple Authentication for Grade 12 IT Students of Iligan Computer Institute.

## How To Use

Copy this script in your mysql cli.

```
  CREATE DATABASE simpleauth;
  
  CREATE TABLE users (
  id int primary key auto_increment,
  username varchar(50) not null unique,
  password varchar(255) not null,
  created_at datetime default current_timestamp
  );
```
