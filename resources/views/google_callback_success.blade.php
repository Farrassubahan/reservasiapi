<!DOCTYPE html>
<html>
<head>
    <title>Regist sukses</title>
</head>
<body>
<script>
    // Kirim token dan data user ke window opener (frontend Ionic)
    if (window.opener) {
        window.opener.postMessage({
            token: "{{ $token }}",
            nama: "{{ addslashes($nama) }}",
            email: "{{ addslashes($email) }}"
        }, "*");
        window.close(); // Tutup popup
    } else {
        // Jika tidak popup, redirect langsung ke login dengan token
        window.location.href = "http://localhost:8100/login?token={{ $token }}&nama={{ urlencode($nama) }}&={{ urlencode($nama) }}&email={{ urlencode($email) }}";
    }
</script>
</body>
</html>
