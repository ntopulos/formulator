<?= $debug ?>
<?= $open ?>

<? foreach ($elements as $table => $items): ?>
<? foreach ($items as $element): ?>

    <? $div_class = (!$element->dataValid() ? ' class="invalid"' : ''); ?>

    <div<?= $div_class; ?>>
        <? if(!$element->hidden AND $element->renderLabel()): ?>
            <?= $element->renderLabel(); ?>
        <? endif; ?>
        <div>
            <?= $element->renderElement() ?>
            <? if(!$element->dataValid() AND $element->dataError()): ?>
                <p class="error"><?= $element->dataError(); ?></p>
            <? endif; ?>
        </div>
    </div>

<?  endforeach; ?>
<?  endforeach; ?>

<div>
    <?
    foreach($final_buttons as $button) {
        echo $button;
    }
    echo $close; ?>
</div>