# 09. Insufficent logging and monitoring

### [Introduction]

[-] When web application is deployed, every actions of user should be logged

### [?] Why do we to log users’s action?

[-] Once their actions are traced, their risk and impact can be determined. 

[-] If we don’t trace their actions, it will lead to bigger impact like accessing personal user information or attack agains website by stealing credentials, attacking infrastructure , etc.

### [*] The information stored in logs should include:

- HTTP status codes
- Time Stamps
- Usernames
- API endpoints/page locations
- IP addresses

## Suspicious Activities

- Multiple unauthorised attempts for a particular action. (in admin pages)
- Requests from anomalous IP address or locations. (someone else is trying to access a particular user's account - can have false positive rate)
- Use of automated tools (can be identified using user-agent headers or speed of requests)
- Common payloads (XSS, SQLi, etc)

**Note:** The suspicious activity needs to be rated according to the impact level. Higher impact actions need to be responded sooner.

[Answer questions]

- This is file log:

```
200 OK           12.55.22.88 jr22          2019-03-18T09:21:17 /login
200 OK           14.56.23.11 rand99        2019-03-18T10:19:22 /login
200 OK           17.33.10.38 afer11        2019-03-18T11:11:44 /login
200 OK           99.12.44.20 rad4          2019-03-18T11:55:51 /login
200 OK           67.34.22.10 bff1          2019-03-18T13:08:59 /login
200 OK           34.55.11.14 hax0r         2019-03-21T16:08:15 /login
401 Unauthorised 49.99.13.16 admin         2019-03-21T21:08:15 /login
401 Unauthorised 49.99.13.16 administrator 2019-03-21T21:08:20 /login
401 Unauthorised 49.99.13.16 anonymous     2019-03-21T21:08:25 /login
401 Unauthorised 49.99.13.16 root          2019-03-21T21:08:30 /login 
```

What IP address is the attacker using?

**Result:** 49.99.13.16 (Check for common actions in a short sequence of time)

What kind of attack is being carried out?

**Result:** bruteforce (trying to brute different username from the same IP addr)
