<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistematica-Unasp</title>
    <style>
        /* Reset e variáveis */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        :root {
            --navy-blue: #000080;
            --orange: #FFA500;
            --white: #FFFFFF;
            --light-gray: #F7FAFC;
            --text-gray: #4A5568;
            --border-color: rgba(0, 0, 0, 0.1);
        }

        body {
            min-height: 100vh;
            display: flex;
            background-color: var(--light-gray);
            color: var(--text-gray);
        }

        /* Background image container */
        .background-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1;
            overflow: hidden;
        }

        .background-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            filter: brightness(0.7);
        }

        .content-wrapper {
            display: flex;
            width: 100%;
            height: 100vh;
            backdrop-filter: blur(10px);
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: rgba(0, 0, 128, 0.8);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            z-index: 100;
            backdrop-filter: blur(5px);
        }

        .institution-name {
            font-size: 18px;
            font-weight: 600;
        }

        aside {
            width: 300px;
            height: 100vh;
            background-color: rgba(255, 255, 255, 0.9);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            padding: 80px 24px 24px;
            z-index: 90;
        }

        .background-aside {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 20px;
        }

        .nav-button {
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            background: var(--orange);
            color: var(--white);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            max-width: 200px;
            box-shadow: 0 4px 6px rgba(255, 165, 0, 0.1);
        }
      
        .nav-button:hover {
            background: #FF8C00;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(255, 165, 0, 0.2);
        }

        /* Modal/Popup styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 1000;
        }

        .modal-content {
            position: relative;
            background: var(--white);
            width: 90%;
            max-width: 400px;
            margin: 10vh auto;
            padding: 32px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .close-button {
            position: absolute;
            right: 24px;
            top: 24px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--text-gray);
            transition: color 0.3s ease;
        }

        .close-button:hover {
            color: var(--navy-blue);
        }

        .modal-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 24px;
            color: var(--navy-blue);
            text-align: center;
        }

        /* Estilos do formulário */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-gray);
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--orange);
            box-shadow: 0 0 0 3px rgba(255, 165, 0, 0.1);
        }

        .btn-primary {
            background-color: var(--orange);
            color: var(--white);
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 16px;
        }

        .btn-primary:hover {
            background-color: #FF8C00;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(255, 165, 0, 0.2);
        }

        .forgot-password {
            display: block;
            margin-top: 16px;
            color: var(--navy-blue);
            text-decoration: none;
            text-align: center;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: var(--orange);
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .content-wrapper {
                flex-direction: column;
            }

            aside {
                width: 100%;
                height: auto;
                border-right: none;
                border-bottom: 1px solid var(--border-color);
                padding: 80px 24px 24px;
            }

            .background-aside {
                flex-direction: row;
                justify-content: center;
                padding: 20px 0;
            }
        }
    </style>
</head>
<body>
    <div class="background-container">
        <img src="img/bg-img.png" alt="Background">
    </div>

    <div class="content-wrapper">
        <header class="header">
            <div class="institution-name">Unasp - HT</div>
        </header>

        <aside>
            <div class="background-aside">
                <button class="nav-button" onclick="openModal('loginModal')">Login</button>
            </div>
        </aside>

        <main>
            <!-- Conteúdo principal aqui -->
        </main>
    </div>

    <!-- Modal de Login -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <button class="close-button" onclick="closeModal('loginModal')">&times;</button>
            <h2 class="modal-title">Login</h2>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email do Usuário:</label>
                    <input type="text" name="email" id="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" name="senha" id="senha" class="form-control" required>
                </div>
                <button type="submit" class="btn-primary">Login</button>
            </form>
            <a href="recuperar_senha.php" class="forgot-password">Esqueci minha senha</a>
        </div>
    </div>
    
    <script>
        // Funções para controlar os modals
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Fechar modal ao clicar fora dele
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }

        // Fechar modal com a tecla ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                document.querySelectorAll('.modal').forEach(modal => {
                    modal.style.display = 'none';
                });
                document.body.style.overflow = 'auto';
            }
        });
    </script>
</body>
</html>