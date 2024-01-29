<!doctype html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, shrink-to-fit=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Интернет-магазин — Трёшка.</title>
    <?php $this->getStyles()?>
    <meta name="description" content="<?=$this->set['description']?>">
    <meta name="keywords" content="<?=$this->set['keywords']?>">
    <meta name="google" content="notranslate">
    <meta name="referrer" content="none-when-downgrade">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?=SITE_URL?>">
    <meta property="og:title" content="Интернет-магазин — Трёшка.">
    <meta property="og:description" content="<?=$this->set['description']?>">
    <meta property="og:image" content="<?=SITE_URL . $this->alias($this->set['img'])?>">
</head>

<body>
<header class="header">
    <div class="container">
        <div class="header__wrapper">

            <div class="header__logo">
                <a href="<?=$this->alias()?>"><img src="<?=$this->img($this->set['img'])?>" alt="<?=$this->set['name']?>"></a>
                <span><?=$this->set['name']?></span>
            </div>
            <div class="header__topbar">
                <div class="header__contacts">
                    <div><a href="mailto:<?=$this->set['email']?>"><?=$this->set['email']?></a></div>
                    <div><a href="tel:<?=preg_replace('/[^+\d]/', '', $this->set['phone'])?>"><?=$this->set['phone']?></a></div>
                    <div><a class="js-callback">Связаться с нами</a></div>
                </div>
                <nav class="header__nav">
                    <ul class="header__nav-list">

                    <?php if (!empty($this->menu['catalog'])):?>
                        <li class="header__nav-parent">
                            <a href="<?=$this->alias('catalog')?>"><span>Каталог</span></a>
                            <ul class="header__nav-sublist">
                                <?php foreach ($this->menu['catalog'] as $item):?>
                                    <li class="">
                                        <a href="<?=$this->alias(['catalog' => $item['alias']])?>"><span><?=$item['name']?></span></a>
                                    </li>
                                <?php endforeach;?>
                            </ul>
                        </li>
                    <?php endif;?>

                    <?php if (!empty($this->menu['information'])):?>
                        <?php foreach ($this->menu['information'] as $item):?>
                            <li class="header__nav-parent">
                                <a href="<?=$this->alias(['information' => $item['alias']])?>"><span><?=$item['name']?></span></a>
                                <ul class="header__nav-sublist">

                                </ul>
                            </li>
                        <?php endforeach;?>
                    <?php endif;?>

                        <li class="header__nav-parent">
                            <a href="<?=$this->alias('news')?>"><span>Новости</span></a>
                            <ul class="header__nav-sublist">

                            </ul>
                        </li>
                        <li class="header__nav-parent">
                            <a href="<?=$this->alias('contacts')?>"><span>Контакты</span></a>
                            <ul class="header__nav-sublist">

                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="overlay"></div>
            <div class="header__sidebar">
                <div class="header__sidebar_btn">
                    <a href="<?=$this->alias('cart')?>" class="cart-btn-wrap">
                        <svg class="inline-svg-icon svg-basket">
                            <use href="<?=PATH . TEMPLATE?>assets/img/icons.svg#basket"></use>
                        </svg>
                        <span data-totalQty><?=$this->cart['total_qty'] ?? 0?></span>
                    </a>
                </div>
                <div class="header__sidebar_btn burger-menu">
                    <div class="burger-menu__link">
                        <span class="burger"></span>
                        <span class="burger-desc">меню</span>
                    </div>
                </div>
                <div class="header__sidebar_btn">
                    <a href="<?=$this->userData ? $this->alias('profile') : '#'?>" <?=!$this->userData ? 'data-popup="login-popup"' : ''?>>
                        <img src="<?=PATH . TEMPLATE?>assets/img/user.png" alt="<?=$item['name'] ?? ''?>">
                    </a>
                </div>
                <?php if (!empty($this->social_networks)):?>
                    <?php foreach ($this->social_networks as $item):?>
                    <div class="header__sidebar_btn">
                        <a href="<?=$this->alias($item['external_alias'])?>" rel="noopener" target="_blank">
                            <img src="<?=$this->img($item['img'])?>" alt="<?=$item['name']?>">
                        </a>
                    </div>
                    <?php endforeach;?>
                <?php endif;?>
            </div>
            <div class="header__menu _hidden">
                <div class="header__menu_close close_modal"></div>
                <ul class="header__menu_burger">
                    <?php if (!empty($this->menu['catalog'])):?>
                        <li>
                            <a href="<?=$this->alias('catalog')?>"><span>Каталог</span></a>
                            <ul class="header__menu_sublist">
                                <?php foreach ($this->menu['catalog'] as $item):?>
                                    <li>
                                        <a href="<?=$this->alias(['catalog' => $item['alias']])?>"><span><?=$item['name']?></span></a>
                                    </li>
                                <?php endforeach;?>
                            </ul>
                        </li>
                    <?php endif;?>

                    <?php if (!empty($this->menu['information'])):?>
                        <?php foreach ($this->menu['information'] as $item):?>
                            <li>
                                <a href="<?=$this->alias(['information' => $item['alias']])?>"><span><?=$item['name']?></span></a>

                                <ul class="header__menu_sublist">

                                </ul>

                            </li>
                        <?php endforeach;?>
                    <?php endif;?>

                    <li>
                        <a href="<?=$this->alias('news')?>"><span>Новости</span></a>

                        <ul class="header__menu_sublist">

                        </ul>

                    </li>

                    <li>
                        <a href="<?=$this->alias('contacts')?>"><span>Контакты</span></a>

                        <ul class="header__menu_sublist">

                        </ul>

                    </li>

                </ul>
            </div>
            <div class="header__callback _hidden">
                <div class="header__callback_close close_modal"></div>
                <div class="header__callback_header">
                    Связаться с нами
                </div>
                <form class="header__callback_form" method="post" <?=$this->alias('send-mail')?>>
                    <label>
                        <input type="text" class="input-text header__callback_input" placeholder="Ваше имя">
                    </label>
                    <label>
                        <input type="email" class="input-text header__callback_input" placeholder="E-mail">
                    </label>
                    <label>
                        <input type="text" class="input-text header__callback_input js-mask-phone" placeholder="Телефон">
                    </label>
                    <div class="header__callback_privacy">
                        <label class="checkbox">
                            <input type="checkbox" />
                            <span class="checkbox__text">Соглашаюсь с правилами обработки персональных данных</span>
                        </label>
                    </div>
                    <button type="submit" class="form-submit header__callback_submit">Отправить</button>
                </form>
            </div>
        </div>
    </div>
</header>

<?php if ($this->getController() !== 'index'):?>
    <form class="search search-internal" action="<?=$this->alias('search')?>">
        <button>
            <svg class="inline-svg-icon svg-search">
                <use xlink:href="<?=PATH . TEMPLATE?>/assets/img/icons.svg#search"></use>
            </svg>
        </button>
        <label class="search search-internal">
            <input type="search" name="search" placeholder="Поиск по каталогу" autocomplete="off" autocapitalize="off" spellcheck="false">
        </label>
    </form>
<?php endif;?>

<div class="login-popup">
    <div class="login-popup__inner">
        <h2><span>Регистрация</span><span>Вход</span></h2>
        <form method="post" action="<?=$this->alias(['login' => 'registration'])?>">
            <label for="name">
                <input type="text" name="name" autocomplete="name" required placeholder="Ваш логин" value="<?=$this->setFormValues('name', 'userData')?>">
            </label>
            <label for="password">
                <input type="password" name="password" autocomplete="new-password" required placeholder="Ваш пароль">
            </label>
            <label for="password">
                <input type="password" name="confirm_password" autocomplete="new-password" required placeholder="Подтверждение пароля">
            </label>
            <label for="phone">
                <input type="tel" name="phone" autocomplete="phone" required placeholder="Номер телефона" value="<?=$this->setFormValues('phone', 'userData')?>">
            </label>
            <label for="email">
                <input type="email" name="email" autocomplete="email" required placeholder="Электронная почта" value="<?=$this->setFormValues('email', 'userData')?>">
            </label>
            <label class="send-login">
                <button class="execute-login_btn" type="submit">Зарегистрироваться</button>
            </label>
        </form>
        <form method="post" action="<?=$this->alias(['login' => 'login'])?>" style="display: none;">
            <label for="login">
                <input type="text" name="login" autocomplete="login" required placeholder="Номер телефона или электронная почта" value="<?=$this->setFormValues('phone')?>">
            </label>
            <label for="password">
                <input type="password" name="password" autocomplete="current-password" required placeholder="Подтверждение пароля">
            </label>
            <label class="send-login">
                <button class="execute-login_btn" type="submit">Вход</button>
            </label>
        </form>
    </div>
</div>

<?php if (!empty($_SESSION['result']['answer'])):?>
    <div class="wq-message__wrap"><?=$_SESSION['result']['answer']?></div>
<?php endif;?>
<?php unset($_SESSION['result']);?>

<main class="main">