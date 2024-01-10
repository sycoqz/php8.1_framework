<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Страница авторизации</title>

    <style>

        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;

        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        div {
            flex-basis: 500px;
            padding: 15px;
        }

        form {
            display: block;
        }

        label, input {
            display: block;
            margin: auto;
        }

        label, h1 {
            text-align: center;
        }

        input {
            margin-bottom: 20px;
        }

        input[type=submit] {
            background: #FFF;
            padding: 8px 10px;
            border: 1px solid black;
        }

        input[type=submit]:hover {
            background: rgba(33,42,55,0.36);
        }

    </style>

</head>
<body>

    <div>

        <?php if (!empty($_SESSION['result']['answer'])) {

            echo '<p style="color: red">' . $_SESSION['result']['answer'] . '</p>';

            unset($_SESSION['result']);

        }?>

        <h1>Авторизации</h1>
        <form action="<?=PATH . $adminPath?>/login" method="post">
            <label for="login">Логин</label>
            <input type="text" name="login" id="login">
            <label for="password">Пароль</label>
            <input type="password" name="password" id="password">
            <input type="submit" value="Войти">
        </form>
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

