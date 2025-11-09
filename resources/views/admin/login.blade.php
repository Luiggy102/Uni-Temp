<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container">
        <div class="row justify-content-center" style="margin-top: 100px;">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0">Acceso de admin Ecotec IOT</h4>
                    </div>
                    <div class="card-body p-4">
                        
                        <form action="{{ route('admin.login.attempt') }}" method="POST">
                            @csrf <div class="mb-3">
                                <label for="username" class="form-label">Usuario</label>
                                <input type="text" class="form-control" id="username" name="username" required autofocus>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Ingresar</button>
                            </div>
                        </form>
                        
                        @if ($errors->any())
                            <div class="alert alert-danger mt-3 mb-0">
                                {{ $errors->first('message') }}
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>