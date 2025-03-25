<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Deteksi Kehadiran</title>
</head>
<body>
    <h2>Deteksi Kehadiran Karyawan</h2>

    <p id="status">Mendeteksi lokasi...</p>
    <p id="distanceInfo"></p>

    @if(!$office)
        <p><strong>Lokasi kantor belum diatur.</strong></p>
    @else
        <p>📍 <strong>Alamat Kantor:</strong> {{ $office->address }}</p>
        <p>🌐 <strong>Koordinat:</strong> ({{ $office->latitude }}, {{ $office->longitude }})</p>

        <script>
            function checkLocation() {
                const officeLat = {{ $office->latitude }};
                const officeLng = {{ $office->longitude }};

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition((position) => {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;

                        const distance = getDistance(userLat, userLng, officeLat, officeLng);
                        const distanceMeters = (distance * 1000).toFixed(2);

                        if (distance <= 1.0) {
                            document.getElementById("status").innerText = "✅ Status: WFO (Dalam jangkauan)";
                        } else {
                            document.getElementById("status").innerText = "🏠 Status: WFH (Di luar jangkauan)";
                        }

                        document.getElementById("distanceInfo").innerText = "Jarak ke kantor: " + distanceMeters + " meter";
                    }, () => {
                        alert("Gagal mendeteksi lokasi. Aktifkan GPS & izinkan akses lokasi.");
                        document.getElementById("status").innerText = "❌ Lokasi tidak dapat dideteksi.";
                    });
                } else {
                    alert("Browser tidak mendukung geolocation.");
                    document.getElementById("status").innerText = "❌ Geolocation tidak didukung.";
                }
            }

            function getDistance(lat1, lon1, lat2, lon2) {
                const R = 6371;
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLon = (lon2 - lon1) * Math.PI / 180;
                const a = Math.sin(dLat / 2) ** 2 +
                          Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                          Math.sin(dLon / 2) ** 2;
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return R * c;
            }

            window.onload = checkLocation;
        </script>
    @endif
</body>
</html>
