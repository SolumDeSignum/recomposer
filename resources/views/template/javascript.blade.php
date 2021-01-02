<script>
    $(document).ready(function () {
        $('.table').DataTable({
            responsive: true,
            'order': [[0, 'desc']],
            searchHighlight: true
        });

        let domGeneratedReport = document.getElementById("generatedReport").value;
        domGeneratedReport = domGeneratedReport.replace(/(^\s*)|(\s*$)/gi, "");
        domGeneratedReport = domGeneratedReport.replace(/[ ]{2,}/gi, " ");
        domGeneratedReport = domGeneratedReport.replace(/\n /, "\n");
        document.getElementById("generatedReport").value = domGeneratedReport;

        $("#copyGeneratedReport").on('click', function () {
            $("#generatedReport").select();
            document.execCommand("copy");
        });
    });
</script>
