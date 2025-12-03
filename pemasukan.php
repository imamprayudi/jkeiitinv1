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

    <title>Data Pemasukan</title>
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

echo "<h3>Selamat datang, " . $_SESSION['user'] . "</h3>";
?>


<div class="container-fluid mt-3">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Data Pemasukan</h4>
        </div>

        <div class="card-body">

            <form>

                <div class="mb-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" id="startdate" name="startdate" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">End Date</label>
                    <input type="date" id="enddate" name="enddate" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jenis Dokumen</label>
                    <select id="jenisdok" name="jenisdok" class="form-select" required>
                        <option selected value="ALL">ALL</option>
                        <option value="23BC">23BC</option>
                        <option value="26BC">26BC</option>
                        <option value="27BC">27BC</option>
                        <option value="27GB">27GB</option>
                        <option value="40BC">40BC</option>
                        <option value="262BC">262BC</option>

                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">BC Number</label>
                    <input type="text" id="bcnumber" name="bcnumber" class="form-control" placeholder="Masukkan BC Number">
                </div>

                <div class="mb-3">
                    <label class="form-label">Part Number</label>
                    <input type="text" id="partnumber" name="partnumber" class="form-control" placeholder="Masukkan Part Number">
                </div>

                <button type="submit" class="btn btn-primary">
                    Submit
                </button>

            </form>

        </div>
    </div>
    
</div>
<button id="btnCsv" class="btn btn-success mb-3">Download CSV</button>

<div class="table-responsive">
<table id="dataTable" class="table table-bordered table-striped table-hover">
      <thead id="thead" class="table-dark text-center align-middle"></thead>
      <tbody id="tbody"></tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>

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
let urlmasukdetail = '';
let data = [];   // <-- variabel global

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
  urlmasukdetail = data.urlmasukdetail;  
}) 
.catch(err => console.error(err));

async function getMasukDetail(tglawal,tglakhir,jenisdok,nomorbc,partno)
{
  let awal = tglawal;
  let akhir = tglakhir;
  try 
  {
    const response = await fetch(urlmasukdetail, 
    {
      method: 'POST',
      credentials: "include",
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({
        tglawal: awal,
        tglakhir: akhir,
        jnsdok: jenisdok,
        nobc : nomorbc,
        part : partno
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
        <th rowspan="2" class="text-end">NO</th>
        <th rowspan="2" class="text-center">JENIS DOK</th>

        <th colspan="2" class="text-center">DOKUMEN PABEAN</th>
        <th colspan="2" class="text-center">BUKTI PENERIMAAN BARANG</th>

        <th rowspan="2" class="text-center">PEMASOK</th>
        <th rowspan="2" class="text-center">PARTNO</th>
        <th rowspan="2" class="text-center">PARTNAME</th>
        <th rowspan="2" class="text-center">SAT</th>
        <th rowspan="2" class="text-center">JUMLAH</th>
        <th rowspan="2" class="text-center">MATA UANG</th>
        <th rowspan="2" class="text-center">NILAI</th>
    </tr>

    <tr>
        <th class="text-center">NOMOR</th>
        <th class="text-center">TANGGAL</th>

        <th class="text-center">NOMOR</th>
        <th class="text-center">TANGGAL</th>
    </tr>
    `;
    thead.innerHTML += judul;
    let datarow = ``;
    data.forEach((item, index) => 
    {
      datarow += `<tr>
      <td align="right">${index + 1}</td>
      <td>${item.jnsdok}</td>
      <td>${item.dpno}</td>
      <td>${item.dptgl.substring(0, 10)}</td>
      <td>${item.bpbno}</td>
      <td>${item.bpbtgl.substring(0, 10)}</td>
      <td>${item.pemasok}</td>
      <td><pre>${item.partno}</pre></td>
      <td>${item.partname}</td>
      <td>${item.sat}</td>
      <td align="right">${Number(item.jumlah).toFixed(0)}</td>
      <td>${item.currency}</td>
      <td align="right">${Number(item.nilai).toFixed(2)}</td>
      </tr>`;
    });
    tbody.innerHTML += datarow;
  } catch (error) 
  {        
    console.error(error);
  }

}
// ---------------------------------------------------------------------------
document.addEventListener('submit', function(e)
{
  e.preventDefault();
  const tglawal = document.getElementById('startdate').value;
  const tglakhir = document.getElementById('enddate').value;
  const dokjenis = document.getElementById('jenisdok').value;
  const nomorbc = document.getElementById('bcnumber').value;
  const partno = document.getElementById('partnumber').value;
  let awal = new Date(tglawal);
  let akhir   = new Date(tglakhir);
  if (awal > akhir) 
  {
    alert("The start date cannot be greater than the end date!");
    return;
  } 
  getMasukDetail(tglawal,tglakhir,dokjenis,nomorbc,partno);

});

document.getElementById("btnCsv").addEventListener("click", () => {
    const csv = convertToCSV(data);  // ambil data JSON hasil fetch
    downloadCSV("pemasukan.csv", csv);
});

</script>
</body>
</html>

