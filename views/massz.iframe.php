<?php declare(strict_types=1);

/**
 * MASSZ - Iframe View
 *
 * @var CView $this
 * @var array $data
 */

use Modules\MASSZ\Helpers\MasszTranslator as T;

?>
<div class="massz-iframe-container">
    <div class="massz-iframe-loader" id="massz-loader">
        <div class="massz-spinner"></div>
        <div class="massz-loader-text"><?= T::t('Carregando aplicação externa...') ?></div>
    </div>
    
    <iframe 
        id="massz-iframe" 
        class="massz-responsive-iframe" 
        src="<?= htmlspecialchars($data['url']) ?>"
        <?php if ($data['sandbox'] !== ''): ?>
            sandbox="<?= htmlspecialchars($data['sandbox']) ?>"
        <?php endif; ?>
        <?php if ($data['allow_permissions'] !== ''): ?>
            allow="<?= htmlspecialchars($data['allow_permissions']) ?>"
        <?php endif; ?>
        <?php if ($data['referrer_policy'] !== ''): ?>
            referrerpolicy="<?= htmlspecialchars($data['referrer_policy']) ?>"
        <?php endif; ?>
        onload="document.getElementById('massz-loader').style.display = 'none';"
    ></iframe>

    <div class="massz-iframe-fallback" id="massz-fallback" style="display: none;">
        <p><?= T::t('Se o sistema não carregar corretamente, clique aqui para abrir em uma nova aba.') ?></p>
        <a href="<?= htmlspecialchars($data['url']) ?>" target="_blank" class="btn-alt"><?= T::t('Nova Aba') ?></a>
    </div>
</div>

<script type="text/javascript">
// Exibe o fallback de link externo caso o iframe demore mais de 4 segundos para sumir com o loader (ex: X-Frame-Options bloquear)
// Shows external link fallback if loader is still visible after 4 seconds (e.g. if blocked by X-Frame-Options)
setTimeout(function() {
    var loader = document.getElementById('massz-loader');
    if (loader && loader.style.display !== 'none') {
        document.getElementById('massz-fallback').style.display = 'block';
    }
}, 4000);
</script>
