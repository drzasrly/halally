<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OCR Scanner</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        #preview {
            max-width: 400px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h2>Upload Gambar untuk OCR</h2>

    <input type="file" id="fileInput" accept="image/*">
    <br><br>
    <button id="scanBtn">Scan</button>

    <div id="previewContainer"></div>
    <h3>Hasil OCR:</h3>
    <pre id="ocrResult"></pre>

    <script>
        let selectedFile = null;

        $("#fileInput").on("change", function () {
            const file = this.files[0];
            if (file) {
                selectedFile = file;
                const reader = new FileReader();
                reader.onload = function (e) {
                    $("#previewContainer").html(`<img id="preview" src="${e.target.result}">`);
                };
                reader.readAsDataURL(file);
            }
        });

        $("#scanBtn").on("click", function () {
            if (!selectedFile) {
                alert("Pilih gambar terlebih dahulu!");
                return;
            }

            let formData = new FormData();
            formData.append("image", selectedFile);

            $.ajax({
                url: "ocr-proses.php", // Pastikan file ini ADA di folder yang benar
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $("#ocrResult").text("Memproses OCR...");
                },
                success: function (response) {
                    $("#ocrResult").text(response);
                },
                error: function () {
                    $("#ocrResult").text("Gagal memproses OCR.");
                }
            });
        });
    </script>
</body>
</html>
