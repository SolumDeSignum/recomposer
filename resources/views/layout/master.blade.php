<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel ReComposer</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.0-beta1/css/bootstrap.min.css"
          integrity="sha512-thoh2veB35ojlAhyYZC0eaztTAUhxLvSZlWrNtlV01njqs/UdY3421Jg7lX0Gq9SRdGVQeL8xeBp9x1IPyL1wQ=="
          crossorigin="anonymous"/>
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/datatables-plugins/1.10.22/features/searchHighlight/dataTables.searchHighlight.min.css"
          integrity="sha512-UdB3UnPgiH4Jd4aTT3gko/77d/InWZeSVoCpxvVeKIAAhKIQdiHojoov9uH8TMnGwHA9g77I/rVPU/KhJQVqAQ=="
          crossorigin="anonymous"/>
    @include('solumdesignum/recomposer::template.stylesheet')
</head>
<body>

@yield('content')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"
        integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg=="
        crossorigin="anonymous"></script>
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"
    integrity="sha512-BkpSL20WETFylMrcirBahHfSnY++H2O1W+UnEEO4yNIl+jI2+zowyoGJpbtk6bx97fBXf++WJHSSK2MV4ghPcg=="
    crossorigin="anonymous"></script>
<script
    src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap5.min.js"></script>
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.0-beta1/js/bootstrap.bundle.min.js"
    integrity="sha512-q2vREMvON/xrz1KuOj5QKWmdvcHtM4XNbNer+Qbf4TOj+RMDnul0Fg3VmmYprdf3fnL1gZgzKhZszsp62r5Ugg=="
    crossorigin="anonymous"></script>
<script src="https://bartaz.github.io/sandbox.js/jquery.highlight.js"></script>
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/datatables-plugins/1.10.22/features/searchHighlight/dataTables.searchHighlight.min.js"
    integrity="sha512-ZHwl/sXF83CO/DW7xmwtExUm47hem9o3Vd+cGALFS69TRZaQGF+bW7meHHvB9QuQalHptERlYzy92ixf5OlDpw=="
    crossorigin="anonymous"></script>
@include('solumdesignum/recomposer::template.javascript')
</body>
</html>
