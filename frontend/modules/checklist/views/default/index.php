<?php

$this->title = 'checklists';

/**
 * @var array $checklists чеклисты
 */
?>
<section class=checklists>
    <? foreach ($checklists as $checklist): ?>
        <div class="item">
            <a href="#!" data-target="<?= $checklist['id'] ?>" class="item__name">
                <?= $checklist['name']?>
            </a>
            <div class="item__created_at">
                <?= $checklist['created_at']?>
            </div>
            <div class="item__updated_at">
                <?= $checklist['updated_at']?>
            </div>
            <div class="item__status">
                <?= $checklist['status']?>
            </div>
        </div>
    <?endforeach; ?>
</section>
