<!DOCTYPE html>
<?php
session_start();
  if (!isset($_SESSION['user'])) {
    // Jika session tidak ada, redirect ke login
   header("Location: login.php");
    exit();
}
   
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>MUTASI SCRAP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    .table-responsive {
        max-height: 500px;
        overflow-y: auto;
        overflow-x: auto;
    }

    /* Semua header sticky */
    thead th {
        position: sticky;
        z-index: 15;
        background-color: #212529 !important;
        color: white;
    }

    /* Baris pertama tetap di top:0 */
    thead tr:first-child th {
        top: 0;
        z-index: 20;
    }
    table {
    font-size: 12px;
}

</style>
<body class="bg-light">
  <?php include 'menu.php';
?>
<h3 class="mb-3">LAPORAN MUTASI BULANAN</h3>

<div class="container-fluid mt-3">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">MUTASI SCRAP</h4>
        </div>

        <div class="card-body">

            <form>

                

                <div class="mb-3">
                    <label class="form-label">PERIODE</label>
                    <select id="periode" name="periode" class="form-select" required>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kode Barang</label>
                    <input type="text" id="partnumber" name="partnumber" class="form-control" placeholder="Masukkan Kode Barang">
                </div>
<div id="loading" class="text-center d-none">
    <div class="spinner-border text-primary" role="status"></div>
    <div>Memuat data...</div>
</div>
                <div class="d-flex">
    <button type="submit" class="btn btn-primary">
        Submit
    </button>

    <button id="btnCsv" type="button" class="btn btn-success ms-auto">
        Download CSV
    </button>
</div>
            </form>
        </div>
    </div>
</div>

<div class="table-responsive">
<table id="dataTable" class="table table-bordered table-striped table-hover">
      <thead id="thead" class="table-dark text-center align-middle"></thead>
      <tbody id="tbody"></tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>

  function showLoading() {
    document.getElementById("loading").classList.remove("d-none");
}

function hideLoading() {
    document.getElementById("loading").classList.add("d-none");
}

// Convert array object ke CSV
function convertToCSV(data) {
    if (!data || data.length === 0) return "";

    const header = Object.keys(data[0]).join(",") + "\n";

    const rows = data.map(row =>
        Object.values(row).map(value => {
            // handle koma, kutip, dan baris baru
            if (typeof value === "string") {
                value = value.replace(/"/g, '""');
                return `"${value}"`;
            }
            return value;
        }).join(",")
    ).join("\n");

    return header + rows;
}

// Download CSV ke file
function downloadCSV(filename, csvText) {
    const blob = new Blob([csvText], { type: "text/csv;charset=utf-8;" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    link.click();
}

let user = '';
let level = '';
let urlperiode = '';
let urlscrap = '';
let data = [];   // <-- variabel global
let mutasi = 'SC';

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
  urlperiode = data.urlperiode;
  urlscrap = data.urlscrap;  
  getPeriode(mutasi);
}) 
.catch(err => console.error(err));

async function getPeriode(jenismutasi)
{
  try 
  {
    const response = await fetch(urlperiode, 
    {
      method: 'POST',
      credentials: "include",
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({
        mutasi: jenismutasi,
      })
    });

    const reply = await response.text(); // ambil balasan dari PHP
    const isidata = JSON.parse(reply);    
    const selecttgl = document.getElementById('periode');
    isidata.forEach((item, index) => {
    const option = document.createElement('option');
    option.value = item.periode;       // nilai option
    option.textContent = item.periode; // teks yang 
    if (index === 0) {
      option.selected = true;
    }
    selecttgl.appendChild(option);
    });    
  } catch (error) 
  {        
    console.error(error);
  }
}


async function getScrap(per,kodebrg)
{
  showLoading();
  try 
  {
    const response = await fetch(urlscrap, 
    {
      method: 'POST',
      credentials: "include",
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({
        periode: per,
        kodebarang: kodebrg
      })
    });
  
    const reply = await response.json();
    data = reply.data;  
    const table = document.getElementById("dataTable");
    const thead = table.querySelector("thead");
    const tbody = table.querySelector("tbody");
    thead.innerHTML = "";
    tbody.innerHTML = "";
    let judul = `
    <tr>
        <th class="text-end">NO</th>
        <th class="text-center">KODE BARANG</th>

        <th class="text-center">NAMA BARANG</th>
        <th class="text-end">JUMLAH</th>
        <th class="text-center">PERIODE</th>
    </tr>
    `;
    thead.innerHTML += judul;
    let datarow = ``;
    data.forEach((item, index) => 
    {
      datarow += `<tr>
      <td align="right">${index + 1}</td>
      <td><pre>${item.kodebarang}</pre></td>
      <td>${item.namabarang}</td>
      <td align="right">${Number(item.jumlah).toFixed(0)}</td>
      <td align="center">${item.periode}</td>
      </tr>`;
    });
    tbody.innerHTML += datarow;
  } catch (error) 
  {        
    console.error(error);
  }finally 
  {
    hideLoading();
  }

}
// ---------------------------------------------------------------------------
document.addEventListener('submit', function(e)
{
  e.preventDefault();
  const period = document.getElementById('periode').value;
  const partno = document.getElementById('partnumber').value;
  getScrap(period,partno);

});

document.getElementById("btnCsv").addEventListener("click", () => {
    const csv = convertToCSV(data);  // ambil data JSON hasil fetch
    downloadCSV("scrap.csv", csv);
});

</script>
</body>
</html>

