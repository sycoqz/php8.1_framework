</main>
    <footer class="footer">
        <div class="container">
            <div class="footer__wrapper">
                <div class="footer__left">
                    <div class="footer__qrcode">
                        <img src="<?=$this->img($this->set['qrcode'])?>" alt="qrcode">
                        <p>Наведите камеру и скачайте бесплатное приложение <?=$this->set['name']?></p>
                        <?php if (!empty($this->mobile_apps)):?>
                            <div class="footer__mobile_apps">
                                <?php foreach ($this->mobile_apps as $item):?>
                                    <a href="<?=$this->alias($item['external_alias'])?>" rel="noopener" target="_blank">
                                        <img src="<?=$this->img($item['img'])?>" alt="<?=$item['name']?>">
                                    </a>
                                <?php endforeach;?>
                            </div>
                        <?php endif;?>
                    </div>
                </div>
                <div class="footer__right">
                    <div class="footer__menu">
                        <div class="footer__menu_list">
                            <span class="footer__help">Помощь</span>
                            <a href="<?=$this->alias('catalog')?>" target="_blank">Каталог</a>
                            <a href="<?=$this->alias('payment')?>" target="_blank">Оплата</a>
                            <a href="<?=$this->alias('delivery')?>" target="_blank">Доставка</a>
                            <a href="<?=$this->alias('refund')?>" target="_blank">Возврат товаров</a>
                            <a href="<?=$this->alias('contacts')?>" target="_blank">Контакты</a>
                        </div>
                        <div class="footer__top_contacts">
                            <div>
                                <a href="<?=$this->alias('contacts')?>"><?=$this->set['email']?></a>
                                <a href="<?=$this->alias('contacts')?>"><?=$this->set['phone']?></a>
                                <a class="js-callback">Связаться с нами</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="footer__social_networks">
                        <?php if (!empty($this->social_networks)):?>
                            <?php foreach ($this->social_networks as $item):?>
                                <a href="<?=$this->alias($item['external_alias'])?>" rel="noopener" target="_blank">
                                    <img src="<?=$this->img($item['img'])?>" alt="<?=$item['name']?>">
                                </a>
                            <?php endforeach;?>
                        <?php endif;?>
                    </div>
                    <div class="footer__bottom_copy">&copy <?=$this->set['ooo_content'] ?? 'ООО ' . '"' . $this->set['name'] . '"'?>
                        Все права защищены.
                    </div>
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

<?php $this->getScripts()?>

    </body>
</html>