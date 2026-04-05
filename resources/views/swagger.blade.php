<!DOCTYPE html>
<html>
<head>
    <title>API Docs</title>
    <link rel="stylesheet" href="/swagger-ui/swagger-ui.css" />
</head>
<body>
    <div id="swagger-ui"></div>

    <script src="/swagger-ui/swagger-ui-bundle.js"></script>
    <script>
        window.onload = () => {
            SwaggerUIBundle({
                url: "/openapi.json",
                dom_id: "#swagger-ui",
            });
        };
    </script>
</body>
</html>