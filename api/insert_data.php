<?php
include '../koneksi.php';

// ==========================================
// AMBIL DATA ESP32
// ==========================================
$suhu = $_POST['suhu'];
$kelembapan = $_POST['kelembapan'];
$getaran = $_POST['getaran'];
$kemiringan = $_POST['kemiringan'];
$sw420 = $_POST['sw420'];
$status = $_POST['status'];
$penyebab = $_POST['penyebab'];
$boleh_kirim_wa = $_POST['boleh_kirim_wa'];

// ==========================================
// INSERT DATABASE
// ==========================================
$query = mysqli_query($koneksi,
    "INSERT INTO tb_monitor
    (
        suhu,
        kelembapan,
        getaran,
        kemiringan,
        sw420,
        status,
        penyebab
    )
    VALUES
    (
        '$suhu',
        '$kelembapan',
        '$getaran',
        '$kemiringan',
        '$sw420',
        '$status',
        '$penyebab'
    )"
);

// ==========================================
// NORMALISASI KATEGORI
// ==========================================
$kategori = strtolower($penyebab);
$kategori = trim(strtolower($kategori));
$status = trim(strtoupper($status));

if ($kategori == "suhu kelembapan") {
    $kategori = "suhu dan kelembapan";
}
else if ($kategori == "getaran kemiringan") {
    $kategori = "getaran dan kemiringan";
}



// ==========================================
// AMBIL DATA MITIGASI
// ==========================================
$queryMitigasi = mysqli_query($koneksi,
    "SELECT * FROM tb_mitigasi
    WHERE kategori='$kategori'
    AND status='$status'
");

/*$dataMitigasi = mysqli_fetch_assoc($queryMitigasi);
$dampak = $dataMitigasi['dampak'];
$saran = $dataMitigasi['saran'];
$sumber = $dataMitigasi['sumber'];*/

$dataMitigasi = mysqli_fetch_assoc($queryMitigasi);
if ($dataMitigasi) {
    $dampak = $dataMitigasi['dampak'];
    $saran = $dataMitigasi['saran'];
    $sumber = $dataMitigasi['sumber'];
}
else {
    $dampak = "-";
    $saran = "-";
    $sumber = "-";
}

// ==========================================
// FORMAT WAKTU
// ==========================================
date_default_timezone_set('Asia/Jakarta');
$waktu = date('d-m-Y H:i:s');

// ==========================================
// PARAMETER DINAMIS
// ==========================================
$parameter = "";

// ==========================================
// SUHU
// ==========================================
if ($kategori == "suhu") {
    $parameter .= "Suhu : $suhu °C\n";
}
// ==========================================
// KELEMBAPAN
// ==========================================
else if ($kategori == "kelembapan") {
    $parameter .= "Kelembapan : $kelembapan %\n";
}
// ==========================================
// GETARAN
// ==========================================
else if ($kategori == "getaran") {
    $parameter .= "Getaran : $getaran m/s2\n";
}
// ==========================================
// KEMIRINGAN
// ==========================================
else if ($kategori == "kemiringan") {
    $parameter .= "Kemiringan : $kemiringan °\n";
}
// ==========================================
// SUHU DAN KELEMBAPAN
// ==========================================
else if ($kategori == "suhu dan kelembapan") {
    $parameter .= "Suhu : $suhu °C\n";
    $parameter .= "Kelembapan : $kelembapan %\n";
}
// ==========================================
// GETARAN DAN KEMIRINGAN
// ==========================================
else if ($kategori == "getaran dan kemiringan") {
    $parameter .= "Getaran : $getaran m/s2\n";
    $parameter .= "Kemiringan : $kemiringan °\n";
}


// ==========================================
// TEMPLATE PESAN
// ==========================================
$pesan =
"PERINGATAN DINI GEDUNG LAWANG SEWU\n\n".
"Status : ".ucwords($kategori)." $status\n\n".
$parameter."\n".
"Dampak :\n$dampak\n\n".
"Saran :\n$saran\n\n".
"Sumber :\n$sumber\n\n".
"Waktu : $waktu";


// ==========================================
// RESPONSE
// ==========================================
if ($query) {
    echo "\nBERHASIL\n";
}

//Debug
echo "\nKategori: ".$kategori;
echo "\nParameter : ".$parameter;
echo "\nStatus : ".$status;

// ==========================================
// FONNTE
// ==========================================
$qFonnte = mysqli_query($koneksi, "SELECT nomor_wa, token FROM tb_fonnte LIMIT 1");
$fonnte = mysqli_fetch_assoc($qFonnte);
$token = $fonnte['token'] ?? "";
$target = $fonnte['nomor_wa'] ?? "";

// ==========================================
// KIRIM WHATSAPP
// ==========================================
if ($boleh_kirim_wa == 1) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.fonnte.com/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,

        CURLOPT_POSTFIELDS => array(
            'target' => $target,
            'message' => $pesan,
        ),

        CURLOPT_HTTPHEADER => array(
            "Authorization: $token"
        ),
    ));

    $response = curl_exec($curl);
//    echo $response;
// ==========================================
// STATUS KIRIM
// ==========================================
    $status_kirim = "GAGAL";
    if ($response) {
        $jsonResponse = json_decode($response, true);
        if (isset($jsonResponse['status']) && $jsonResponse['status'] === true) {
            $status_kirim = "BERHASIL";
        }
    }
    curl_close($curl);
    // ==========================================
    // SIMPAN LOG NOTIFIKASI
    // ==========================================
    mysqli_query($koneksi,
        "INSERT INTO tb_notifikasi
        (
            pesan,
            status_kirim,
            log_fonnte
        )
        VALUES
        (
            '$pesan',
            '$status_kirim',
            '$response'
        )"
    );
    
    echo "\nWA TERKIRIM\n";
}

else {
    echo "\nWA TIDAK TERKIRIM\n";
}
?>