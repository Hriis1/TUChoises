<!-- Jquery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
    crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js"
    integrity="sha384-VQqxDN0EQCkWoxt/0vsQvZswzTHUVOImccYmSyhJTp7kGtPed0Qcx8rK9h9YEgx+"
    crossorigin="anonymous"></script>

<!-- Datatables -->
<script src="https://cdn.datatables.net/2.3.0/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.0/js/dataTables.bootstrap5.js"></script>

<!-- FontAwsome -->
<script src="https://kit.fontawesome.com/c275ff90f1.js" crossorigin="anonymous"></script>

<script>
    //Import functions
    function importData(postUrl, action) {
        // 1. Create and insert a hidden file input restricted to .xls/.xlsx
        const $fileInput = $(
            '<input type="file" accept=".xls,.xlsx" style="display:none;" />'
        );
        $('body').append($fileInput);

        // 2. When the user selects a file...
        $fileInput.on('change', function () {
            const file = this.files[0];
            if (!file) {
                // No file - cleanup
                $fileInput.remove();
                return;
            }

            // 3. Validate extension
            const allowed = ['xls', 'xlsx'];
            const ext = file.name.split('.').pop().toLowerCase();
            if (!allowed.includes(ext)) {
                alert('Please select a .xls or .xlsx file.');
                $fileInput.remove();
                return;
            }

            // 4. Build FormData
            const formData = new FormData();
            formData.append("fileUpload", file);
            formData.append("action", action);

            // 5. Send AJAX POST
            $.ajax({
                url: postUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success(response) {
                    location.reload();
                },
                error(jqXHR, textStatus, errorThrown) {
                    alert('Import failed: ' + (jqXHR.responseText || textStatus));
                    console.error('Error details:', errorThrown);
                },
                complete() {
                    // Remove the input after we done
                    $fileInput.remove();
                }
            });
        });

        // 6. Kick off the file dialog
        $fileInput.click();
    }

    //Main
    $(document).ready(function () {

    });
</script>

</body>

</html>