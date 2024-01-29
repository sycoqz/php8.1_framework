<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Страница авторизации</title>
    <meta name="robots" content="none">
    <link rel="stylesheet" href="/templates/default/assets/css/login.css">
</head>
<body>
    <div class="container">
        <?php if (!empty($_SESSION['result']['answer'])) {

            echo '<p style="color: red;text-align: center">' . $_SESSION['result']['answer'] . '</p>';

            unset($_SESSION['result']);

        }?>
        <?php if (isset($adminPath)):?>
            <form action="<?=PATH . $adminPath?>/login" method="post" class="login-form">
                <h1>Авторизация</h1>
                    <div class="input-field">
                        <label for="login">
                            <input type="text" name="login" id="login" required placeholder="Логин" autocomplete="off">
                            <img class="login" src="<?=PATH . TEMPLATE?>assets/img/login/personsvg.svg" alt="user">
                        </label>
                    </div>
                    <div class="input-field">
                        <label for="password">
                            <input type="password" name="password" id="password" required placeholder="Пароль" autocomplete="off">
                            <img class="password" src="<?=PATH . TEMPLATE?>assets/img/login/locksvg.svg" alt="lock">
                        </label>
                    </div>
                    <div class="login-button">
                        <label>
                            <input type="submit" value="Войти">
                        </label>
                    </div>
            </form>
        <?php endif;?>
    </div>
    <script src="<?=PATH . ADMIN_TEMPLATE?>js/frameworkfunctions.js"></script>
    <script>

        let form = document.querySelector('form')

        if (form) {

            form.addEventListener('submit', e => {

                // Проверка генерации пользователем или программой
                if (e.isTrusted) {

                    e.preventDefault()

                    Ajax({data:{ajax:'token'}}).then(result => {

                        if (result) {

                            form.insertAdjacentHTML('beforeend',`<input type="hidden" name="token" value="${result}">`)

                        }

                        form.submit()

                    })

                }

            })

        }

    </script>
</body>
</html>

