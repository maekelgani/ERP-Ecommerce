<?php
function renderBreadcrumb($items = []) {
    if (empty($items)) return;
    ?>
    <nav class="flex items-center text-sm text-gray-500 mb-3 flex-wrap gap-y-1">
        <a href="../../view/admin/DashboardAdmin.php" class="hover:text-[#882426] transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">home</span>
            Dashboard
        </a>
        <?php foreach ($items as $index => $item): ?>
            <span class="material-symbols-outlined text-gray-400 mx-2 text-sm">chevron_right</span>
            <?php if (isset($item['link'])): ?>
                <a href="<?= htmlspecialchars($item['link']); ?>" class="hover:text-[#882426] transition-colors">
                    <?= htmlspecialchars($item['label']); ?>
                </a>
            <?php else: ?>
                <span class="text-gray-800 font-medium"><?= htmlspecialchars($item['label']); ?></span>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
    <?php
}
?>
