<main class="main-internal">
    <div class="container">
        <nav class="breadcrumbs">
            <ul class="breadcrumbs__list" itemscope="" itemtype="http://schema.org/BreadcrumbList">
                <li class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a class="breadcrumbs__link" itemprop="item" href="<?=PATH?>">
                        <span itemprop="name">Главная</span>
                    </a>
                    <meta itemprop="position" content="1" />
                </li>
                <li class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a class="breadcrumbs__link" itemprop="item" href="catalog-line.html#">
                        <span itemprop="name">корзина</span>
                    </a>
                    <meta itemprop="position" content="2" />
                </li>
            </ul>
        </nav>
        <h1 class="page-title h1">корзина</h1>
    </div>

    <?php if (empty($this->cart['goods'])):?>
        <section class="catalog-internal">
            <div class="container">
                <div class="catalog-internal-wrap">
                    <p>Ваша корзина пуста</p>
                </div>
            </div>
        </section>
    <?php else:?>
        <section class="catalog-internal">
            <div class="container">
                <div class="catalog-internal-wrap">
                    <section class="catalog-section catalog-section__line">

                        <div class="basket-top">

                            <div class="total-basket-price">
                                <?php if (!empty($this->cart['total_old_sum'])):?>
                                Итого: <span class="total-basket-price_old-price" data-totalOldSum><?=$this->cart['total_old_sum']?> руб.</span>
                                <?php endif;?>
                                <span class="total-basket-price_new-price" data-totalSum><?=$this->cart['total_sum']?> руб.</span>
                            </div>
                            <div class="basket-btns">
                                <button class="basket-btn">Перейти к оформлению</button>
                                <a href="<?=$this->alias(['cart' => 'clearAll'])?>" class="basket-btn">Очистить корзину</a>
                            </div>
                        </div>

                        <div class="catalog-section__wrapper">
                            <div class="catalog-section-items">
                                <div class="catalog-section-items__wrapper">
                                    <?php foreach ($this->cart['goods'] as $item):?>
                                        <div class="card-item card-item__internal card-item__line" data-productContainer>
                                            <div class="card-item__tabs_image">
                                                <img src="<?=$this->img($item['img'])?>" alt="">
                                            </div>
                                            <div class="card-item__tabs_description">
                                                <div class="card-item__tabs_name">
                                                    <span><?=$item['name']?></span>
                                                </div>
                                                <div class="card-item__tabs_price">
                                                    Цена:
                                                    <?php if (!empty($item['old_price'])):?>
                                                    <span class="card-item_old-price"><?=$item['old_price']?> руб.</span>
                                                    <?php endif;?>
                                                    <span class="card-item_new-price"><?=$item['price']?> руб.</span>
                                                </div>
                                            </div>
                                            <button class="card-item__btn">
                                                Удалить
                                            </button>
                                            <span class="card-main-info-size__body">
                                              <span class="card-main-info-size__control card-main-info-size__control_minus js-counterDecrement" data-quantityMinus></span>
                                              <span class="card-main-info-size__count js-counterShow" data-quantity><?=$item['qty']?></span>
                                              <span class="card-main-info-size__control card-main-info-size__control_plus js-counterIncrement" data-quantityPlus></span>
                                            </span>
                                            <?php if ($item['hit']):?>
                                                <div class="icon-offer">
                                                    <svg>
                                                        <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#hot"></use>
                                                    </svg>
                                                </div>
                                            <?php endif;?>
                                            <a style="display: none" data-addToCart="<?=$item['id']?>" data-toCartAdded href="#" class="card-main-info__button button-basket button-blue button-big button">
                                            </a>
                                        </div>
                                    <?php endforeach;?>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </section>

        <section class="order-registration">
            <div class="container">
                <form class="order-registration-form">
                    <div class="order-registration-payment">
                        <div class="order-registration-titel">Оплата</div>
                        <div class="order-registration-radio">
                            <label class="order-registration-radio-item">
                                <input class="order-registration-rad-inp" type="radio" name="payment" checked>
                                <div class="order-registration-radio-item-descr">Оплатить заказ наличными при получении</div>
                            </label>
                            <label class="order-registration-radio-item">
                                <input class="order-registration-rad-inp" type="radio" name="payment">
                                <div class="order-registration-radio-item-descr">Оплатить сейчас картой Visa/Mastercard</div>
                            </label>
                        </div>
                    </div>
                    <div class="order-registration-delivery">
                        <div class="order-registration-titel">Доставка</div>
                        <div class="order-registration-radio">
                            <label class="order-registration-radio-item">
                                <input class="order-registration-rad-inp" type="radio" name="deliver" checked>
                                <div class="order-registration-radio-item-descr"><span>Магазин: </span>г. Калуга, ул. Московская 300</div>
                            </label>
                            <label class="order-registration-radio-item">
                                <input class="order-registration-rad-inp" type="radio" name="deliver">
                                <div class="order-registration-radio-item-descr"><span>Диллеры: </span></div>
                            </label>
                        </div>
                    </div>
                    <div class="amount-pay-wrapp">
                        Сумма к оплате:
                        <span class="amount-pay" data-totalSum><?=$this->cart['total_sum']?> руб.</span>
                    </div>
                    <input class="execute-order_btn" type="submit" name="" value="Оформить заказ">
                </form>
            </div>
        </section>
    <?php endif;?>

</main>