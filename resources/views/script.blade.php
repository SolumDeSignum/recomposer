<!-- Initialize & config datatables -->
<script>
    $(document).ready(function() {
        $('#decomposer').DataTable({
            'order': [[0, 'asc']],
            searchHighlight: true
        });

        s = document.getElementById("txt-report").value;
        s = s.replace(/(^\s*)|(\s*$)/gi, "");
        s = s.replace(/[ ]{2,}/gi, " ");
        s = s.replace(/\n /, "\n");
        document.getElementById("txt-report").value = s;

        $('#btn-report').on('click', function() {
            $("#report-wrapper").slideToggle();
        });

        $("#copy-report").on('click', function() {
            $("#txt-report").select();
            document.execCommand("copy");
        });
    });

</script>
