<?= $debug ?>
<?= $open ?>

<? if ($global_error) : ?>
    <div class="error"><?= $global_error ?></div>
<? endif; ?>

<? for ($row=0; $row < $rows; $row++) : ?>
<? foreach ($elements as $table => $items) : ?>
<? foreach ($items as $element) : ?>

    <? $div_class = ((!$element->dataValid($row) && !$global_validation) ? ' class="invalid"' : ''); ?>

    <div<?= $div_class; ?>>
        <? if (!$element->hidden AND $element->renderLabel($row)) : ?>
            <?= $element->renderLabel($row); ?>
        <? endif; ?>
        <div>
            <?= $element->renderElement($row) ?>
            <? if (!$element->dataValid($row) AND $element->dataError($row)) : ?>
                <p class="error"><?= $element->dataError($row); ?></p>
            <? endif; ?>
        </div>
    </div>

<? endforeach; ?>
<? endforeach; ?>
<? endfor; ?>

<div>
    <div></div>
    <div>
    <?
    foreach($buttons as $button) {
        echo $button;
    }
    ?>
    </div>
</div>

</form>