<!doctype html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, shrink-to-fit=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Трёшка</title>
    <?php $this->getStyles()?>

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
                        <a href="<?=$this->alias($item['external_alias'])?>">
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
            <input type="search" name="search" placeholder="Поиск по каталогу">
        </label>
    </form>
<?php endif;?>

<main class="main">