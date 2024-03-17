# 02. Broken authentication practical

[Part 1. Before we start]

As we know, the most common way of user authentication on website are username, password and session cookie right ! 

[+] username, password is very popular for users 

[+]  A session cookie is needed because web servers use HTTP(S) to communicate which is stateless. Attaching session cookies means that the server will know who is sending what data. The server can then keep track of users' actions. ⇒ there is a technique called hijacking to steal user ‘s cookie so that they can login without username and pw

For example:  After logging on successfully into the member area, web server creates a session for the user; The user can access other pages within the member area without having to provide username and password until the end of the session

So what is broken authentication?

[+] We can understand that it is a flaw in a authentication mechanism. It includes bruteforce attack,  use of weak credentials (password too easy to guess), weak session cookies (session cookie too easy to guess )

[Part 2. Exploit]

- Firstly, we will check if the username which is darren exists on server.

Error: This user is already registered ⇒ username exists 

- So according to tryhackme, if create a space before the name like “ darren” and register with whatever pw, we can login as darren

⇒ The flag as darren:  fe86079416a21a3c99937fea8874b667

- Use the same trick with arthur

⇒ The flag as arthur: d9ac0f7db4fda460ac3edeb75d75e16e
