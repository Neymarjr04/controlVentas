<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sistema de Ventas - Login</title>
    <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    rel="stylesheet"
    />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <link rel="stylesheet" href="./styles/login.css" />
    <script defer src="/js/login.js"></script>
  </head>
  <body>
    <div class="login-container" id="loginContainer">
      <div class="login-header">
        <i class="fas fa-store"></i>
        <h1>Sistema de Ventas</h1>
        <p>Abarrotes La esquinita</p>
      </div>
      <form class="login-form" id="loginForm">
        <div id="alertContainer"></div>
        <div class="form-group">
          <input type="text" id="usuario" name="usuario" required />
          <label for="usuario">Usuario</label>
          <i class="fas fa-user"></i>
        </div>
        <div class="form-group">
          <input type="password" id="password" name="password" required />
          <label for="password">Contraseña</label>
          <i class="fas fa-lock"></i>
        </div>
        <button type="submit" class="btn-login">
          <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
        </button>
      </form>
    </div>
  </body>
</html>
