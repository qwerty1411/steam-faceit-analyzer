<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Faceit Auth Success</title>
</head>
<body>
<script>
    // Send user data back to opener and close
    window.opener.postMessage(
        { type: 'faceit-auth-success', user: @json($user) },
        window.location.origin
    );
    window.close();
</script>
</body>
</html>
