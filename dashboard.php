<!DOCTYPE html>
<?php
session_start();

  if (!isset($_SESSION['user'])) {
    // Jika session tidak ada, redirect ke login
   header("Location: login.php");
    exit();
}
   
?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <title>Dashboard</title>
  <link id="favicon" rel="icon" type="image/png" href="assets/gambar/g-green.png">
</head>
<body class="p-4">
<?php include 'menu.php';

echo "<h6>Selamat datang, " . $_SESSION['user'] . "</h6>";
?>
<h6 class="mb-6">Berikut Transaksi di PT JVCKENWOOD ELECTRONICS INDONESIA 
  Berdasarkan Jenis Dokumen</h6>
<h6 class="mb-6">Total Transaksi Pemasukan Bulan Lalu</h6>
<table class="table table-bordered" id="tblInLast">
    <thead id="thead" class="table-dark text-center align-middle"></thead>
      <tbody id="tbody"></tbody>
</table>

<h6 class="mt-5 mb-6">Total Transaksi Pengeluaran Bulan Lalu</h6>
<table class="table table-bordered" id="tblOutLast">
    <thead id="thead" class="table-dark text-center align-middle"></thead>
      <tbody id="tbody"></tbody>
</table>

<h6 class="mt-5 mb-6">Total Transaksi Pemasukan Bulan Berjalan</h6>
<table class="table table-bordered" id="tblInCurrent">
    <thead id="thead" class="table-dark text-center align-middle"></thead>
      <tbody id="tbody"></tbody>
    <tbody></tbody>
</table>

<h6 class="mt-5 mb-6">Total Transaksi Pengeluaran Bulan Berjalan</h6>
<table class="table table-bordered" id="tblOutCurrent">
     <thead id="thead" class="table-dark text-center align-middle"></thead>
      <tbody id="tbody"></tbody>
    <tbody></tbody>
</table>

 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script>

fetch('getsession.php', 
{
  method: 'GET',
  headers: 
  {
  'X-Requested-With': 'XMLHttpRequest'
  }
})
.then(response => response.json())
.then(data => 
{
  user = data.user;
  level = data.level;
  urlsum = data.urlsum;
  getSum('dashboard');
}) 
.catch(err => console.error(err));


async function getSum(period)
{
  try 
  {
    const response = await fetch(urlsum, 
    {
      method: 'POST',
      credentials: "include",
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({
        periode: period,
      })
    });
    const reply = await response.text(); // ambil balasan dari PHP
    const isidata = JSON.parse(reply);   
    const setatus = isidata.status;
    const inpartlastmonth = isidata.inpart_lastmonth;
    const outpartlastmonth = isidata.outpart_lastmonth;
    const inpartcurrent = isidata.inpart_current;
    const outpartcurrent = isidata.outpart_current;
    const tableinlast = document.getElementById("tblInLast");
    const theadinlast = tableinlast.querySelector("thead");
    const tbodyinlast = tableinlast.querySelector("tbody");
    const tableoutlast =  document.getElementById("tblOutLast");
    const theadoutlast = tableoutlast.querySelector("thead");
    const tbodyoutlast = tableoutlast.querySelector("tbody");
    const tblincurrent = document.getElementById("tblInCurrent");
    const theadincurrent = tblincurrent.querySelector("thead"); 
    const tbodyincurrent = tblincurrent.querySelector("tbody");
    const tbloutcurrent = document.getElementById("tblOutCurrent");
    const theadoutcurrent = tbloutcurrent.querySelector("thead"); 
    const tbodyoutcurrent = tbloutcurrent.querySelector("tbody");

    theadinlast.innerHTML = "";
    tbodyinlast.innerHTML = "";
    theadoutlast.innerHTML = "";
    tbodyoutlast.innerHTML = "";
    theadincurrent.innerHTML = "";
    tbodyincurrent.innerHTML = "";
    theadoutcurrent.innerHTML = "";
    tbodyoutcurrent.innerHTML = "";
    let judul = `
    <tr>
        <th class="text-center">JENIS DOKUMEN</th>
        <th class="text-end">TOTAL</th>
    </tr>
    `;
    theadinlast.innerHTML += judul;
    theadoutlast.innerHTML += judul;
    theadincurrent.innerHTML += judul;
    theadoutcurrent.innerHTML += judul;


  let datarowinlast = ``;
    inpartlastmonth.forEach((item, index) => 
    {
      datarowinlast += `<tr>
      <td><pre>${item.jenisdok}</pre></td>
      <td align="right">${item.total}</td>
      </tr>`;
    });
    tbodyinlast.innerHTML += datarowinlast;

    let datarowoutlast = ``;
    outpartlastmonth.forEach((item, index) => 
    {
      datarowoutlast += `<tr>
      <td><pre>${item.jenisdok}</pre></td>
      <td align="right">${item.total}</td>
      </tr>`;
    });
    tbodyoutlast.innerHTML += datarowoutlast;

    let datarowincurrent = ``;
    inpartcurrent.forEach((item, index) => 
    {
      datarowincurrent += `<tr>
      <td><pre>${item.jenisdok}</pre></td>
      <td align="right">${item.total}</td>
      </tr>`;
    });
    tbodyincurrent.innerHTML += datarowincurrent;

    let datarowoutcurrent = ``;
    outpartcurrent.forEach((item, index) => 
    {
      datarowoutcurrent += `<tr>
      <td><pre>${item.jenisdok}</pre></td>
      <td align="right">${item.total}</td>
      </tr>`;
    });
    tbodyoutcurrent.innerHTML += datarowoutcurrent;
  } catch (error) 
  {        
    console.error(error);
  }
}

</script>
</body>
</html>

