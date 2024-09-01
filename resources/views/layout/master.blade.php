<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel ReComposer</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/v/bs5/dt-2.1.5/datatables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/datatables-plugins/1.10.22/features/searchHighlight/dataTables.searchHighlight.min.css"
          integrity="sha512-UdB3UnPgiH4Jd4aTT3gko/77d/InWZeSVoCpxvVeKIAAhKIQdiHojoov9uH8TMnGwHA9g77I/rVPU/KhJQVqAQ=="
          crossorigin="anonymous"/>
    @include('solumdesignum/recomposer::template.stylesheet')
</head>
<body>

@yield('content')

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/v/bs5/dt-2.1.5/datatables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://bartaz.github.io/sandbox.js/jquery.highlight.js"></script>
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/datatables-plugins/1.10.22/features/searchHighlight/dataTables.searchHighlight.min.js"
    integrity="sha512-ZHwl/sXF83CO/DW7xmwtExUm47hem9o3Vd+cGALFS69TRZaQGF+bW7meHHvB9QuQalHptERlYzy92ixf5OlDpw=="
    crossorigin="anonymous">
</script>
@include('solumdesignum/recomposer::template.javascript')
</body>
</html>
