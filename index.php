<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/configs/db.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/views/layouts/_main_header.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/repairs.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/clients.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/statuses.php');

$repairsModel = isset($params_database_main) ? new Repairs($params_database_main) : new Repairs();
$clientsModel = isset($params_database_main) ? new Clients($params_database_main) : new Clients();
$statusModel = isset($params_database_main) ? new Statuses($params_database_main) : new Statuses();

$currentYear = intval(date('Y'));
$currentMonth = intval(date('m'));

$repairsThisMonth = $repairsModel->count_repairs_in_month($currentYear, $currentMonth);
$prevMonth = $currentMonth - 1;
$prevYear = $currentYear;
if ($prevMonth === 0) {
    $prevMonth = 12;
    $prevYear--;
}
$repairsPrevMonth = $repairsModel->count_repairs_in_month($prevYear, $prevMonth);
$repairMonthDelta = $repairsThisMonth - $repairsPrevMonth;
if ($repairsPrevMonth === 0) {
    if ($repairsThisMonth === 0) {
        $repairsMonthCaption = 'Минулого місяця також 0 ремонтів';
        $repairsMonthPercent = 0;
    } else {
        $repairsMonthCaption = "На {$repairMonthDelta} ремонтів більше, ніж минулого місяця";
        $repairsMonthPercent = 200;
    }
} else {
    $percentChange = round(($repairMonthDelta / $repairsPrevMonth) * 100);
    if ($repairMonthDelta > 0) {
        $repairsMonthCaption = "На {$repairMonthDelta} ремонтів більше (+{$percentChange}% від попереднього місяця)";
    } elseif ($repairMonthDelta < 0) {
        $repairsMonthCaption = "На " . abs($repairMonthDelta) . " ремонтів менше (" . abs($percentChange) . "% від попереднього місяця)";
    } else {
        $repairsMonthCaption = 'Рівно стільки ж, як минулого місяця';
    }
    $repairsMonthPercent = max(0, 100 + $percentChange);
}

$workingStatuses = ['Нове замовлення', 'Діагностика', 'Очікує узгодження', 'Узгоджено'];
$readyForPickupStatuses = ['Виконано', 'Скасовано', 'Відмовлено'];
$issuedStatuses = ['Видано', 'Видано без ремонту'];

$newRepairs = $repairsModel->count_repairs_by_status_names(['Нове замовлення']);
$diagnosticsRepairs = $repairsModel->count_repairs_by_status_names(['Діагностика']);
$awaitingApprovalRepairs = $repairsModel->count_repairs_by_status_names(['Очікує узгодження']);
$approvedRepairs = $repairsModel->count_repairs_by_status_names(['Узгоджено']);
$readyForPickupRepairs = $repairsModel->count_repairs_by_status_names($readyForPickupStatuses);
$issuedRepairs = $repairsModel->count_repairs_by_status_names($issuedStatuses);
$clientsCount = $clientsModel->count_clients();

$isSuperadmin = isset($_SESSION['account']['role_name']) && $_SESSION['account']['role_name'] === 'superadmin';

$status_new_id = $statusModel->get_id_by_name('Нове замовлення');
$status_diagnostics_id = $statusModel->get_id_by_name('Діагностика');
$status_awaiting_id = $statusModel->get_id_by_name('Очікує узгодження');
$status_approved_id = $statusModel->get_id_by_name('Узгоджено');
$status_done_id = $statusModel->get_id_by_name('Виконано');
$status_issued_id = $statusModel->get_id_by_name('Видано');
$status_vidano_bez_remontu_id = $statusModel->get_id_by_name('Видано без ремонту');

$activeRepairsTotal = $newRepairs + $diagnosticsRepairs + $awaitingApprovalRepairs + $approvedRepairs + $readyForPickupRepairs;

$issuedThisMonth = $repairsModel->count_repairs_by_status_names_in_month($issuedStatuses, $currentYear, $currentMonth);
$issuedPrevMonth = $repairsModel->count_repairs_by_status_names_in_month($issuedStatuses, $prevYear, $prevMonth);
$issuedDelta = $issuedThisMonth - $issuedPrevMonth;
if ($issuedPrevMonth === 0) {
    if ($issuedThisMonth === 0) {
        $issuedCaption = 'Минулого місяця також 0 виданих ремонтів';
        $issuedPercent = 100;
    } else {
        $issuedCaption = "На {$issuedDelta} виданих більше, ніж минулого місяця";
        $issuedPercent = 200;
    }
} else {
    $percentChange = round(($issuedDelta / $issuedPrevMonth) * 100);
    if ($issuedDelta > 0) {
        $issuedCaption = "На {$issuedDelta} виданих більше (+{$percentChange}% від попереднього місяця)";
    } elseif ($issuedDelta < 0) {
        $issuedCaption = "На " . abs($issuedDelta) . " виданих менше (" . abs($percentChange) . "% від попереднього місяця)";
    } else {
        $issuedCaption = 'Рівно стільки ж виданих, як минулого місяця';
    }
    $issuedPercent = max(0, 100 + $percentChange);
}

$overviewMetrics = [
    ['title' => 'Видано цього місяця', 'value' => $issuedThisMonth, 'color' => '#2d3436', 'progress' => true, 'caption' => $issuedCaption, 'percent' => $issuedPercent, 'show_tag' => true, 'link' => '/app/views/repairs.php?status=' . intval($status_issued_id)],
    ['title' => 'Нові ремонтів', 'value' => $newRepairs, 'color' => '#fd79a8', 'progress' => false, 'show_tag' => true, 'link' => '/app/views/repairs.php?status=' . intval($status_new_id)],
    ['title' => 'На діагностиці', 'value' => $diagnosticsRepairs, 'color' => '#0984e3', 'progress' => false, 'show_tag' => true, 'link' => '/app/views/repairs.php?status=' . intval($status_diagnostics_id)],
    ['title' => 'Очікує узгодження', 'value' => $awaitingApprovalRepairs, 'color' => '#ffb142', 'progress' => false, 'show_tag' => true, 'link' => '/app/views/repairs.php?status=' . intval($status_awaiting_id)],
    ['title' => 'Узгоджено', 'value' => $approvedRepairs, 'color' => '#6c5ce7', 'progress' => false, 'show_tag' => true, 'link' => '/app/views/repairs.php?status=' . intval($status_approved_id)],
    ['title' => 'Виконано', 'value' => $readyForPickupRepairs, 'color' => '#00b894', 'progress' => false, 'show_tag' => true, 'link' => '/app/views/repairs.php?status=' . intval($status_done_id)],
    ['title' => 'Клієнтів у базі', 'value' => $clientsCount, 'color' => '#d63031', 'progress' => false, 'show_tag' => false, 'link' => $isSuperadmin ? '/app/views/admin.php?tab=clients' : false],
];

$statusMetrics = [
    ['title' => 'Нові замовлення', 'value' => $newRepairs, 'color' => '#fd79a8', 'status_id' => $status_new_id],
    ['title' => 'На діагностиці', 'value' => $diagnosticsRepairs, 'color' => '#0984e3', 'status_id' => $status_diagnostics_id],
    ['title' => 'Очікує узгодження', 'value' => $awaitingApprovalRepairs, 'color' => '#ffb142', 'status_id' => $status_awaiting_id],
    ['title' => 'Узгоджено', 'value' => $approvedRepairs, 'color' => '#6c5ce7', 'status_id' => $status_approved_id],
    ['title' => 'Готові до видачі', 'value' => $readyForPickupRepairs, 'color' => '#00b894', 'status_id' => $status_done_id],
];
?>

<div class="panel dashboard_panel">
    <h2>Огляд</h2>
        <div class="dashboard_charts">
            <div class="chart_card">
                <div class="chart_card_header">
                    <h3>Стан активних ремонтів</h3>
                    <span class="chart_subtitle"><?php echo $activeRepairsTotal; ?> активних позицій</span>
                </div>
                <div class="status_bars">
                    <?php foreach ($statusMetrics as $status): ?>
                        <?php $statusPercent = round(($status['value'] / max($activeRepairsTotal, 1)) * 100); ?>
                        <div class="status_bar_item">
                            <div class="status_bar_heading">
                                <span><?php echo htmlspecialchars($status['title'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <span><?php echo $status['value']; ?></span>
                            </div>
                            <div class="status_bar_wrap">
                                <div class="status_bar_fill" style="width: <?php echo $statusPercent; ?>%; background: <?php echo $status['color']; ?>;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
    
    <div class="overview_grid">
        <?php foreach ($overviewMetrics as $metric): ?>
            <?php $cardContent = function() use ($metric) {
                ob_start(); ?>
                <div class="overview_card_header<?php echo $metric['show_tag'] ? '' : ' no_tag'; ?>">
                    <h3><?php echo htmlspecialchars($metric['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <?php if ($metric['show_tag']): ?>
                        <span class="overview_tag" style="background: <?php echo $metric['color']; ?>;"></span>
                    <?php endif; ?>
                </div>
                <div class="overview_value"><?php echo $metric['value']; ?></div>
                <?php if (!empty($metric['progress'])): ?>
                    <?php $percent = max(0, intval($metric['percent'])); ?>
                    <?php $basePercent = min($percent, 100); ?>
                    <?php $overflowPercent = $percent > 100 ? min($percent - 100, 100) : 0; ?>
                    <div class="overview_progress">
                        <div class="overview_progress_fill" style="width: <?php echo $basePercent; ?>%; background: <?php echo $metric['color']; ?>;"></div>
                        <?php if ($overflowPercent > 0): ?>
                            <div class="overview_progress_overflow" style="width: <?php echo $overflowPercent; ?>%;"></div>
                        <?php endif; ?>
                    </div>
                    <div class="overview_caption"><?php echo htmlspecialchars($metric['caption'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php else: ?>
                    <div class="overview_progress">
                        <div class="overview_progress_fill" style="width: 0%; background: <?php echo $metric['color']; ?>;"></div>
                        <div class="overview_progress_overflow" style="width: 0%;"></div>
                    </div>
                    <div class="overview_caption overview_caption_filler">&nbsp;</div>
                <?php endif; ?>
                <?php return ob_get_clean();
            };
            $content = $cardContent(); ?>
            <?php if (!empty($metric['link'])): ?>
                <a class="overview_card_link" href="<?php echo htmlspecialchars($metric['link'], ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="overview_card"><?php echo $content; ?></div>
                </a>
            <?php else: ?>
                <div class="overview_card"><?php echo $content; ?></div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/views/layouts/_main_footer.php');
?>
