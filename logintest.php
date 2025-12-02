<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
<script>
let urllogin = 'http://136.198.117.118/api/jkeiitinv1dev/login.php';

async function login(userid,pass)
{
  let iduser = userid;
  let userpass = pass;
  try 
  {
    const response = await fetch(urllogin, 
    {
      method: 'POST',
      credentials: "include",
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({
        userid: iduser,
        password: userpass 
      })
    });
    const reply = await response.text();
    console.log(reply);
  
  }catch (error)
  {
    console.error(error);
  }
}
login('31530','31530');
</script>
</body>
</html>