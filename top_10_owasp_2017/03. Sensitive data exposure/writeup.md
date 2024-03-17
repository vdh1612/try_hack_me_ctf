# 03. Sensitive data exposure practical

[Part 1. Before we start]

- The most common way to store a large amount of data in a format that is easily accessible from many locations at once is in a database like sql or nosql.
- However, instead of setting up on servers for database such as mysql or mariadb, mongodb, the dev store db as files. These databases are referred to as "flat-file" databases, as they are stored as a single file on the computer. ⇒ easier than setting up a full database server

[?] What happens if the database is stored underneath the root directory of the website (i.e. one of the files that a user connecting to the website is able to access)?

- The user can download file.db and check the data in it ⇒ quite dangerous right !
- The most common (and simplest) format of flat-file database is an sqlite database.

[Part 2. Exploit]

Target: http://10.10.112.12/

1) What is the name of the mentioned directory? 

- Ctrl + u to open the source code of /login:

```
┌──(kali㉿kali)-[~/Downloads]
└─$ curl http://10.10.112.12/login/            

<!DOCTYPE html>
<html>
        <head>
              ....
                <script src="../assets/js/loginScript.js"></script>
        </head>
        <body>
                <header>
                        <a id="home" href="/">Sense and Sensitivity</a>
                        <a id="login" href="/login">Login</a>
                </header>
                <div class=background></div>
//[*] /asset is mentioned dir right 
                <!-- Must remember to do something better with the database than store it in /assets... -->
                .....
</html>

```

**Result:** /asset

2) Navigate to the directory you found in question one. What file stands out as being likely to contain sensitive data?

![Screenshot 2024-02-25 165511](https://github.com/vdh1612/CTF_write_up/assets/125654739/deac636d-6347-4e55-96a1-e438dc503240)


**Result:** webapp.db is the file contains sensitive data

3) Use the supporting material to access the sensitive data. What is the password hash of the admin user?

- If i open the file and cat the file, it just shows some data that i don’t really understand right now
- Now we will check database version of the file webapp.db

```
┌──(kali㉿kali)-[~/Downloads]
└─$ ls -l webapp.db 
-rw-r--r-- 1 kali kali 28672 Feb 25 04:56 webapp.db
                                                                                                                                                               
┌──(kali㉿kali)-[~/Downloads]
└─$ file webapp.db  
webapp.db: SQLite 3.x database, last written using SQLite version 3022000, file counter 255, database pages 7, 1st free page 5, free pages 1, cookie 0x6, schema 4, UTF-8, version-valid-for 255
//[*] sqlite database right
```

- Access the file

```
┌──(kali㉿kali)-[~/Downloads]
└─$ sqlite3 webapp.db           
SQLite version 3.40.1 2022-12-28 14:03:47
Enter ".help" for usage hints.
sqlite> 
```

- Show all tables and dump information from table

```
sqlite> .tables
sessions  users   
sqlite> select * from users;
4413096d9c933359b898b6202288a650|admin|6eea9b7ef19179a06954edd0f6c05ceb|1
23023b67a32488588db1e28579ced7ec|Bob|ad0234829205b9033196ba818f7a872b|1
4e8423b514eef575394ff78caed3254d|Alice|268b38ca7b84f44fa0a6cdc86e6301e0|0
```

- The syntax is a little different from mysql so i don’t know which column is this data represented for. So  in cheatsheet, we can use PRAGMA table_info(name_table); to show name of columns

```
sqlite> PRAGMA table_info(users);
0|userID|TEXT|1||1
1|username|TEXT|1||0
2|password|TEXT|1||0
3|admin|INT|1||0
```

- **Column Order:**
    - userID, username, password, admin
- **Data:**
    1. **`userID`**:
        - 4413096d9c933359b898b6202288a650
        - 23023b67a32488588db1e28579ced7ec
        - 4e8423b514eef575394ff78caed3254d
    2. **`username`**:
        - admin
        - Bob
        - Alice
    3. **`password`**:
        - 6eea9b7ef19179a06954edd0f6c05ceb
        - ad0234829205b9033196ba818f7a872b
        - 268b38ca7b84f44fa0a6cdc86e6301e0
    4. **`admin`**:
        - 1
        - 1
        - 0

**Result:** password hash of the admin user <=> ****6eea9b7ef19179a06954edd0f6c05ceb

4) Crack the hash

- Using https://crackstation.net/ to crack hash code

```

Hash	                            Type	Result
6eea9b7ef19179a06954edd0f6c05ceb    md5	      qwertyuiop
```

- Login as admin:

+) Username: admin

+) Password: qwertyuiop

**Final result:** Login as admin ⇒ THM{Yzc2YjdkMjE5N2VjMzNhOTE3NjdiMjdl}
