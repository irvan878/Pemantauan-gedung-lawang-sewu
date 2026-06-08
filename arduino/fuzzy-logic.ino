#include <Wire.h>
#include <DHT.h>
#include <Adafruit_MPU6050.h>
#include <Adafruit_Sensor.h>
#include <WiFi.h>
#include <HTTPClient.h>

// PIN SENSOR
#define DHTPIN 4
#define DHTTYPE DHT22
#define SW420_PIN 15


const char* ssid = "VRSC";
const char* password = "10110010";

// OBJECT SENSOR
DHT dht(DHTPIN, DHTTYPE);
Adafruit_MPU6050 mpu;


// VARIABLE GETARAN
float previousAccel = 0;
int sw420Status;

// MOVING AVERAGE FILTER
const int sampleCount = 5;
float vibrationSamples[sampleCount];
int sampleIndex = 0;

// OFFSET KEMIRINGAN
float kemiringanOffset = 0;

// VARIABLE GLOBAL SENSOR
float suhu = 0;
float kelembapan = 0;
float getaran = 0;
float kemiringan = 0;

// VARIABLE OUTPUT FUZZY
float fuzzyAman = 0;
float fuzzyWaspada = 0;
float fuzzyBahaya = 0;
float nilaiCentroid = 0; // <-- Tambahkan ini untuk menyimpan angka 0-100
String statusFuzzy = "";
String penyebabFuzzy = "";

// VALIDASI STATUS
String lastStatus = "";
String stableStatus = "AMAN";
int statusCounter = 0;

// STATUS NOTIFIKASI
String lastSentStatus = "AMAN";
bool bolehKirimWA = false;

// DATABASE INTERVAL
unsigned long lastDatabaseSend = 0;
unsigned long intervalDatabase = 60000;
bool bolehKirimDatabase = false;
String lastDatabaseStatus = "";

// SETUP
void setup() {
  Serial.begin(115200);
  Serial.println("====================================");
  Serial.println("SISTEM MONITORING LAWANG SEWU");
  Serial.println("====================================");
  // INISIALISASI DHT22
  dht.begin();
  // INISIALISASI SW420
  pinMode(SW420_PIN, INPUT);
  // INISIALISASI MPU6050
  if (!mpu.begin()) {
    Serial.println("MPU6050 gagal terhubung!");
    while (1);
  }
  Serial.println("MPU6050 berhasil terhubung");

  // KALIBRASI AWAL KEMIRINGAN
  sensors_event_t a, g, temp;
  mpu.getEvent(&a, &g, &temp);
  kemiringanOffset = atan2(
                       a.acceleration.y,
                       a.acceleration.z
                     ) * 180 / PI;
  Serial.print("Offset Kemiringan : ");
  Serial.println(kemiringanOffset);

  // KONEK WIFI
  WiFi.begin(ssid, password);
  Serial.print("Menghubungkan WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println();
  Serial.println("WiFi Connected");
  Serial.print("IP Address : ");
  Serial.println(WiFi.localIP());

  // STABILISASI SENSOR
  Serial.println("Stabilisasi sensor 10 detik...");
  delay(10000);
}

// LOOP
void loop() {
  // PROSES FUZZY
  inferensiFuzzy();
  tentukanStatusFuzzy();


  // VALIDASI STATUS
  validasiStatus();
  // CEK TRANSISI STATUS
  cekPengirimanWhatsApp();
  // CEK DATABASE
  cekPengirimanDatabase();

  // OUTPUT SERIAL MONITOR
  // FUZZY SUHU
  float suhu_rendah = suhuRendah(suhu);
  float suhu_sedang = suhuSedang(suhu);
  float suhu_tinggi = suhuTinggi(suhu);
  Serial.println("===== FUZZY SUHU =====");
  Serial.print("Rendah : ");
  Serial.println(suhu_rendah);
  Serial.print("Sedang : ");
  Serial.println(suhu_sedang);
  Serial.print("Tinggi : ");
  Serial.println(suhu_tinggi);
  // FUZZY KELEMBAPAN
  float kelembapan_rendah = kelembapanRendah(kelembapan);
  float kelembapan_sedang = kelembapanSedang(kelembapan);
  float kelembapan_tinggi = kelembapanTinggi(kelembapan);
  Serial.println("===== FUZZY KELEMBAPAN =====");
  Serial.print("Rendah : ");
  Serial.println(kelembapan_rendah);
  Serial.print("Sedang : ");
  Serial.println(kelembapan_sedang);
  Serial.print("Tinggi : ");
  Serial.println(kelembapan_tinggi);
  // FUZZY GETARAN
  float getaran_rendah = getaranRendah(getaran);
  float getaran_sedang = getaranSedang(getaran);
  float getaran_tinggi = getaranTinggi(getaran);
  Serial.println("===== FUZZY GETARAN =====");
  Serial.print("Rendah : ");
  Serial.println(getaran_rendah);
  Serial.print("Sedang : ");
  Serial.println(getaran_sedang);
  Serial.print("Tinggi : ");
  Serial.println(getaran_tinggi);
  // FUZZY KEMIRINGAN
  float kemiringan_rendah = kemiringanRendah(kemiringan);
  float kemiringan_sedang = kemiringanSedang(kemiringan);
  float kemiringan_tinggi = kemiringanTinggi(kemiringan);
  Serial.println("===== FUZZY KEMIRINGAN =====");
  Serial.print("Rendah : ");
  Serial.println(kemiringan_rendah);
  Serial.print("Sedang : ");
  Serial.println(kemiringan_sedang);
  Serial.print("Tinggi : ");
  Serial.println(kemiringan_tinggi);

  bacaDHT22();
  bacaMPU6050();
  bacaSW420();
  
  Serial.println("===== HASIL FUZZY =====");
  Serial.print("Status     : ");
  Serial.println(statusFuzzy);
  Serial.print("Penyebab   : ");
  Serial.println(penyebabFuzzy);
  Serial.print("Aman       : ");
  Serial.println(fuzzyAman);
  Serial.print("Waspada    : ");
  Serial.println(fuzzyWaspada);
  Serial.print("Bahaya     : ");
  Serial.println(fuzzyBahaya);
  Serial.print("Centeroid  : ");
  Serial.println(nilaiCentroid);

  Serial.print("Status Valid    : ");
  Serial.println(stableStatus);
  Serial.print("Counter         : ");
  Serial.println(statusCounter);
  Serial.print("Last Sent Status: ");
  Serial.println(lastSentStatus);
  Serial.print("Kirim WhatsApp  : ");
  if (bolehKirimWA) {
    Serial.println("YA");
  }
  else {
    Serial.println("TIDAK");
  }

  //kirimDataKePHP();
  if (bolehKirimDatabase) {
    kirimDataKePHP();
  }
    delay(5000);
}

// FUNGSI BACA DHT22
void bacaDHT22() {
  suhu = dht.readTemperature();
  kelembapan = dht.readHumidity();
  // VALIDASI PEMBACAAN
  if (isnan(suhu) || isnan(kelembapan)) {
    Serial.println("DHT22 gagal dibaca");
    return;
  }
  suhu = suhu - 3.0;
  kelembapan = kelembapan - 10.0;
  // OUTPUT SERIAL MONITOR
  Serial.println("===== DHT22 =====");
  Serial.print("Suhu        : ");
  Serial.print(suhu);
  Serial.println(" °C");
  Serial.print("Kelembapan  : ");
  Serial.print(kelembapan);
  Serial.println(" %");
}

// FUNGSI BACA SW420
void bacaSW420() {
  sw420Status = digitalRead(SW420_PIN);
}

// FUNGSI BACA MPU6050
void bacaMPU6050() {
  sensors_event_t a, g, temp;
  mpu.getEvent(&a, &g, &temp);
  // ACCELEROMETER
  float accelX = a.acceleration.x;
  float accelY = a.acceleration.y;
  float accelZ = a.acceleration.z;

  // TOTAL ACCELERATION
  float totalAccel = sqrt(
                       (accelX * accelX) +
                       (accelY * accelY) +
                       (accelZ * accelZ)
                     );

  // DELTA GETARAN
  float rawGetaran = abs(totalAccel - previousAccel);
  previousAccel = totalAccel;

  // MOVING AVERAGE FILTER
  vibrationSamples[sampleIndex] = rawGetaran;
  sampleIndex++;
  if (sampleIndex >= sampleCount) {
    sampleIndex = 0;
  }
  float filteredGetaran = 0;
  for (int i = 0; i < sampleCount; i++) {
    filteredGetaran += vibrationSamples[i];
  }
  filteredGetaran /= sampleCount;

  // THRESHOLD NOISE
  if (filteredGetaran < 0.05) {
    filteredGetaran = 0;
  }
  getaran = filteredGetaran;

  // HITUNG KEMIRINGAN
  kemiringan = atan2(
                  accelY,
                  accelZ
                ) * 180 / PI;

  // KALIBRASI KEMIRINGAN
  kemiringan = kemiringan - kemiringanOffset;
  // ABS AGAR TIDAK NEGATIF
  kemiringan = abs(kemiringan);
  // OUTPUT SERIAL MONITOR
  Serial.println("===== MPU6050 =====");
  Serial.print("Getaran     : ");
  Serial.print(getaran);
  Serial.println(" m/s^2");
  Serial.print("Kemiringan  : ");
  Serial.print(kemiringan);
  Serial.println(" °");
  Serial.print("SW420 : ");
  Serial.println(sw420Status);
}

// FUZZY SUHU RENDAH
float suhuRendah(float suhu) {
  if (suhu <= 20)
    return 1;
  else if (suhu > 20 && suhu < 30)
    return (30 - suhu) / (30 - 20);
  else
    return 0;
}
// FUZZY SUHU SEDANG
float suhuSedang(float suhu) {
  if (suhu >= 28 && suhu <= 31.5)
    return (suhu - 28) / (31.5 - 28);
  else if (suhu > 31.5 && suhu <= 35)
    return (35 - suhu) / (35 - 31.5);
  else
    return 0;
}
// FUZZY SUHU TINGGI
float suhuTinggi(float suhu) {
  if (suhu <= 33)
    return 0;
  else if (suhu > 33 && suhu < 40)
    return (suhu - 33) / (40 - 33);
  else
    return 1;
}

// FUZZY KELEMBAPAN RENDAH
float kelembapanRendah(float kelembapan) {
  if (kelembapan <= 40)
    return 1;
  else if (kelembapan > 40 && kelembapan < 60)
    return (60 - kelembapan) / (60 - 40);
  else
    return 0;
}

// FUZZY KELEMBAPAN SEDANG
float kelembapanSedang(float kelembapan) {
  if (kelembapan >= 55 && kelembapan <= 65)
    return (kelembapan - 55) / (65 - 55);
  else if (kelembapan > 65 && kelembapan <= 75)
    return (75 - kelembapan) / (75 - 65);
  else
    return 0;
}
// FUZZY KELEMBAPAN TINGGI
float kelembapanTinggi(float kelembapan) {
  if (kelembapan <= 70)
    return 0;
  else if (kelembapan > 70 && kelembapan < 90)
    return (kelembapan - 70) / (90 - 70);
  else
    return 1;
}

// FUZZY GETARAN RENDAH
float getaranRendah(float getaran) {
  if (getaran <= 0)
    return 1;
  else if (getaran > 0 && getaran < 5)
    return (5 - getaran) / 5;
  else
    return 0;
}
// FUZZY GETARAN SEDANG
float getaranSedang(float getaran) {
  if (getaran >= 4 && getaran <= 7)
    return (getaran - 4) / (7 - 4);
  else if (getaran > 7 && getaran <= 10)
    return (10 - getaran) / (10 - 7);
  else
    return 0;
}
// FUZZY GETARAN TINGGI
float getaranTinggi(float getaran) {
  if (getaran <= 9)
    return 0;
  else if (getaran > 9 && getaran < 15)
    return (getaran - 9) / (15 - 9);
  else
    return 1;
}

// FUZZY KEMIRINGAN RENDAH
float kemiringanRendah(float kemiringan) {
  if (kemiringan <= 0)
    return 1;
  else if (kemiringan > 0 && kemiringan < 5)
    return (5 - kemiringan) / 5;
  else
    return 0;
}
// FUZZY KEMIRINGAN SEDANG
float kemiringanSedang(float kemiringan) {
  if (kemiringan >= 4 && kemiringan <= 9.5)
    return (kemiringan - 4) / (9.5 - 4);
  else if (kemiringan > 9.5 && kemiringan <= 15)
    return (15 - kemiringan) / (15 - 9.5);
  else
    return 0;
}
// FUZZY KEMIRINGAN TINGGI
float kemiringanTinggi(float kemiringan) {
  if (kemiringan <= 12)
    return 0;
  else if (kemiringan > 12 && kemiringan < 20)
    return (kemiringan - 12) / (20 - 12);
  else
    return 1;
}

// INFERENSI FUZZY MAMDANI
void inferensiFuzzy() {
  // FUZZY INPUT
  float suhu_rendah = suhuRendah(suhu);
  float suhu_sedang = suhuSedang(suhu);
  float suhu_tinggi = suhuTinggi(suhu);

  float kelembapan_rendah = kelembapanRendah(kelembapan);
  float kelembapan_sedang = kelembapanSedang(kelembapan);
  float kelembapan_tinggi = kelembapanTinggi(kelembapan);

  float getaran_rendah = getaranRendah(getaran);
  float getaran_sedang = getaranSedang(getaran);
  float getaran_tinggi = getaranTinggi(getaran);

  float kemiringan_rendah = kemiringanRendah(kemiringan);
  float kemiringan_sedang = kemiringanSedang(kemiringan);
  float kemiringan_tinggi = kemiringanTinggi(kemiringan);

  // RESET OUTPUT
  fuzzyAman = 0;
  fuzzyWaspada = 0;
  fuzzyBahaya = 0;

  // RULE AMAN
  fuzzyAman = min(
                  min(suhu_rendah,
                      kelembapan_rendah),
                  min(getaran_rendah,
                      kemiringan_rendah)
                );

  // RULE WASPADA
  fuzzyWaspada = max(
                      max(suhu_sedang,
                          kelembapan_sedang),
                      max(getaran_sedang,
                          kemiringan_sedang)
                    );

  // RULE BAHAYA
  fuzzyBahaya = max(
                     max(suhu_tinggi,
                         kelembapan_tinggi),
                     max(getaran_tinggi,
                         kemiringan_tinggi)
                   );
}

// STATUS FUZZY
/*void tentukanStatusFuzzy() {
  if (fuzzyBahaya >= fuzzyWaspada &&
      fuzzyBahaya >= fuzzyAman) {
    statusFuzzy = "BAHAYA";
  }
  else if (fuzzyWaspada >= fuzzyAman) {
    statusFuzzy = "WASPADA";
  }
  else {
    statusFuzzy = "AMAN";
  }
}*/
void tentukanStatusFuzzy() {
  // Langkah A: Menentukan konstanta nilai tengah
  float zAman = 20.0;
  float zWaspada = 55.5;
  float zBahaya = 85.5;

  // Langkah B: Menghitung Rumus Centroid (mu_1 * z_1) + (mu_2 * z_2) + (mu_3 * z_3)
  float pembilang = (fuzzyAman * zAman) + (fuzzyWaspada * zWaspada) + (fuzzyBahaya * zBahaya);
  // Mencari penyebut: mu_1 + mu_2 + mu_3
  float penyebut = fuzzyAman + fuzzyWaspada + fuzzyBahaya;

  // Validasi agar tidak terjadi error pembagian dengan nol
  if (penyebut > 0) {
    nilaiCentroid = pembilang / penyebut;
  } else {
    nilaiCentroid = 0; // Jika tidak ada rules yang aktif, default ke nilai 0 (Aman)
  }

  // Langkah C: Klasifikasi Status
  if (nilaiCentroid >= 0 && nilaiCentroid <= 40) {
    statusFuzzy = "AMAN";
  } else if (nilaiCentroid > 40 && nilaiCentroid <= 70) {
    statusFuzzy = "WASPADA";
  } else if (nilaiCentroid > 70 && nilaiCentroid <= 100) {
    statusFuzzy = "BAHAYA";
  } else {
    statusFuzzy = "AMAN"; 
  }
}

// PENYEBAB FUZZY
void tentukanPenyebabFuzzy() {
  penyebabFuzzy = "";
  if (suhuTinggi(suhu) > 0 ||
      suhuSedang(suhu) > 0) {
    penyebabFuzzy += "Suhu ";
  }

  if (kelembapanTinggi(kelembapan) > 0 ||
      kelembapanSedang(kelembapan) > 0) {
    penyebabFuzzy += "Kelembapan ";
  }

  if (getaranTinggi(getaran) > 0 ||
      getaranSedang(getaran) > 0) {
    penyebabFuzzy += "Getaran ";
  }

  if (kemiringanTinggi(kemiringan) > 0 ||
      kemiringanSedang(kemiringan) > 0) {
    penyebabFuzzy += "Kemiringan ";
  }

  if (penyebabFuzzy == "") {
    penyebabFuzzy = "-";
  }
}

// VALIDASI 3X BERTURUT
void validasiStatus() {
  // JIKA STATUS SAMA
  if (statusFuzzy == lastStatus) {
    statusCounter++;
  }
  // JIKA STATUS BERUBAH
  else {
    statusCounter = 1;
    lastStatus = statusFuzzy;
  }
  // VALID JIKA 3X
  if (statusCounter >= 3) {
    stableStatus = statusFuzzy;
  }
}

// CEK PENGIRIMAN WHATSAPP
void cekPengirimanWhatsApp() {
  bolehKirimWA = false;
  // JIKA STATUS BERUBAH
  if (stableStatus != lastSentStatus) {
    // HANYA WASPADA / BAHAYA
    if (stableStatus == "WASPADA" ||
        stableStatus == "BAHAYA") {
      bolehKirimWA = true;
    }
    // UPDATE STATUS TERAKHIR
    lastSentStatus = stableStatus;
  }
}

// KIRIM DATA KE PHP
void kirimDataKePHP() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    // URL API
    http.begin(
      "http://192.168.5.238/lawangsewu/api/insert_data.php"
    );
    http.addHeader(
      "Content-Type",
      "application/x-www-form-urlencoded"
    );
    // DATA POST
    int statusWA = bolehKirimWA ? 1 : 0;
    String postData =
      "suhu=" + String(suhu) +
      "&kelembapan=" + String(kelembapan) +
      "&getaran=" + String(getaran) +
      "&kemiringan=" + String(kemiringan) +
      "&sw420=" + String(sw420Status) +
      "&status=" + String(statusFuzzy) +
      "&penyebab=" + String(penyebabFuzzy) +
      "&boleh_kirim_wa=" + String(statusWA);
    // POST DATA
    int httpResponseCode =
      http.POST(postData);
    // OUTPUT SERIAL
    Serial.print("HTTP Response : ");
    Serial.println(httpResponseCode);
    String response =
      http.getString();
    Serial.println(response);
    http.end();
  }
  else {
    Serial.println("WiFi Disconnect");
  }
}

// ======================================================
// CEK PENGIRIMAN DATABASE
// ======================================================
/*void cekPengirimanDatabase() {
  bolehKirimDatabase = false;
  // ==========================================
  // WASPADA ATAU BAHAYA
  // ==========================================
  if (statusFuzzy == "WASPADA" ||
      statusFuzzy == "BAHAYA") {
    bolehKirimDatabase = true;
  }
  // ==========================================
  // AMAN -> INTERVAL 1 MENIT
  // ==========================================
  else if (statusFuzzy == "AMAN") {
    if (millis() - lastDatabaseSend >= intervalDatabase) {
      bolehKirimDatabase = true;
      lastDatabaseSend = millis();
    }
  }
}*/

// CEK PENGIRIMAN DATABASE
void cekPengirimanDatabase() {
  bolehKirimDatabase = false;
  // STATUS WASPADA / BAHAYA
  if (stableStatus == "WASPADA" ||
      stableStatus == "BAHAYA") {
    // STATUS BARU
    if (statusFuzzy != lastDatabaseStatus) {
      bolehKirimDatabase = true;
      lastDatabaseStatus = statusFuzzy;
      lastDatabaseSend = millis();
    }
    // STATUS TETAP -> 1 MENIT
    else {
      if (millis() - lastDatabaseSend >= intervalDatabase) {
        bolehKirimDatabase = true;
        lastDatabaseSend = millis();
      }
    }
  }
  // STATUS AMAN
  else if (statusFuzzy == "AMAN") {
    // reset status terakhir abnormal
    lastDatabaseStatus = "AMAN";
    // interval 1 menit
    if (millis() - lastDatabaseSend >= intervalDatabase) {
      bolehKirimDatabase = true;
      lastDatabaseSend = millis();
    }
  }
}
