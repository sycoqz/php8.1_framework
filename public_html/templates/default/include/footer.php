</main>
<footer class="footer">
    <div class="container">
        <div class="footer__wrapper">
            <div class="footer__top">
                <div class="footer__top_logo">
                    <img src="<?=PATH . TEMPLATE?>/assets/img/Logo.svg" alt="">
                </div>
                <div class="footer__top_menu">
                    <ul>

                        <li>
                            <a href="http://somesite.ru/catalog/"><span>Каталог</span></a>
                        </li>

                        <li>
                            <a href="http://somesite.ru/about/"><span>О нас</span></a>
                        </li>

                        <li>
                            <a href="http://somesite.ru/delivery/"><span>Доставка и оплата</span></a>
                        </li>

                        <li>
                            <a href="http://somesite.ru/contacts/"><span>Контакты</span></a>
                        </li>

                        <li>
                            <a href="http://somesite.ru/news/"><span>Новости</span></a>
                        </li>

                        <li>
                            <a href="http://somesite.ru/sitemap/"><span>Карта сайта</span></a>
                        </li>

                    </ul>
                </div>
                <div class="footer__top_contacts">
                    <div><a href="../../../index.php">test@test.ru</a></div>
                    <div><a href="tel:+74842750204">+7 (4842) 75-02-04</a></div>
                    <div><a class="js-callback">Связаться с нами</a></div>
                </div>
            </div>
            <div class="footer__bottom">
                <div class="footer__bottom_copy">Copyright</div>
            </div>
        </div>
    </div>
</footer>

<div class="hide-elems">
    <svg>
        <defs>
            <linearGradient id="rainbow" x1="0" y1="0" x2="50%" y2="50%">
                <stop offset="0%" stop-color="#7282bc" />
                <stop offset="100%" stop-color="#7abfcc" />
            </linearGradient>
        </defs>
    </svg>
</div>

<?php if (!$this->userData):?>
    <div class="login-popup">
        <div class="login-popup__inner">
            <h2><span>Регистрация</span><span>Вход</span></h2>
            <form method="post" action="<?=$this->alias(['login' => 'registration'])?>">
                <label>
                    <input type="text" name="name" required placeholder="Ваш логин" value="<?=$this->setFormValues('name')?>">
                    <input type="password" name="password" required placeholder="Ваш пароль">
                    <input type="password" name="confirm_password" required placeholder="Подтверждение пароля">
                    <input type="tel" name="phone" required placeholder="Номер телефона" value="<?=$this->setFormValues('phone')?>">
                    <input type="email" name="email" required placeholder="Электронная почта" value="<?=$this->setFormValues('email')?>">
                </label>
                <label class="send-login">
                    <input class="execute-login_btn" type="submit" value="Зарегистрироваться">
                </label>
            </form>
            <form method="post" action="<?=$this->alias(['login' => 'login'])?>" style="display: none;">
                <label>
                    <input type="text" name="login" required placeholder="Номер телефона или электронная почта" value="<?=$this->setFormValues('phone')?>">
                    <input type="password" name="password" required placeholder="Подтверждение пароля">
                </label>
                <label class="send-login">
                    <input class="execute-login_btn" type="submit" value="Вход">
                </label>
            </form>
        </div>
    </div>
<?php endif;?>

<?php $this->getScripts()?>

<?php if (!empty($_SESSION['result']['answer'])):?>
    <div class="wq-message__wrap"><?=$_SESSION['result']['answer']?></div>
<?php endif;?>
<?php unset($_SESSION['result']);?>

</body>

</html>