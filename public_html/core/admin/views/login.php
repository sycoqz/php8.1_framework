<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Страница авторизации</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: rgba(50,127,203,0.36);
        }

        .container {
            position: relative;
            max-width: 420px;
            width: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        .container .login-form {
            padding: 60px;
            height: 100vh;
            max-height: 370px;
            align-items: center;
            display: block;
        }

        .container .login-form h1{
            text-align: center;
            padding: 0 0 5px;
            position: relative;
            font-size: 32px;
            font-weight: 700;
        }

        .login-form .input-field {
            position: relative;
            height: 40px;
            width: 100%;
            margin-top: 40px;
        }

        .input-field input{
            position: absolute;
            height: 100%;
            width: 100%;
            padding: 0 35px;
            border: none;
            outline: none;
            font-size: 16px;
            border-bottom: 2px solid #ccc;
            border-top: 2px solid transparent;
            transition: all 0.2s ease;
        }

        .input-field input:is(:focus) {
            border-bottom-color: rgba(50,127,203,0.36);
        }

        .login-button {
            margin-top: 35px;
            margin-left: 36px;
        }

        .login-button input{
            justify-content: center;
            color: black;
            background-color: rgba(50,127,203,0.36);
            position: relative;
            border-radius: 6px;
            height: 42px;
            width: 80%;
            padding: 0 20px;
            outline: none;
            border: none;
            font-size: 18px;
            font-weight: 500;
            border-bottom: 2px solid #ccc;
            border-top: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .login-button input:hover {
            background: rgba(117, 132, 204, 0.36);
        }

    </style>

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
                            <input type="text" name="login" id="login" placeholder="Логин">
                        </label>
                    </div>
                    <div class="input-field">
                        <label for="password">
                            <input type="password" name="password" id="password" placeholder="Пароль">
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

