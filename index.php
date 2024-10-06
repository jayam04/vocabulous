<?php
// index.php (Dashboard)
require_once 'config.php';
require_once 'functions.php';
require_once 'new_functions.php';
include 'header.php';

$total_words = count_total_words($pdo);
$words_to_practice = count_words_to_practice($pdo);
$new_words = count_new_words($pdo);

$stmt = $pdo->query("SELECT SUM(CASE WHEN correct = 1 THEN 1 ELSE 0 END) as correct, COUNT(*) as total FROM practice_stats");
$practice_stats = $stmt->fetch(PDO::FETCH_ASSOC);
$accuracy = $practice_stats['total'] > 0 ? round(($practice_stats['correct'] / $practice_stats['total']) * 100, 2) : 0;

// Prepare data for the chart
$dates_data = [];
$high_data = [];
$medium_data = [];
$low_data = [];
$total_data = [];

$dates = get_different_practice_dates($pdo);

foreach ($dates as $date) {
    $result = get_practice_stats_by_date($pdo, $date['practice_date']);
    
    $dates_data[] = $date['practice_date'];
    $high_data[] = $result[0]['high_accuracy'];
    $medium_data[] = $result[0]['medium_accuracy'];
    $low_data[] = $result[0]['low_accuracy'];
    $total_data[] = $result[0]['high_accuracy'] + $result[0]['medium_accuracy'] + $result[0]['low_accuracy'];
}
?>

<h1 class="text-3xl font-bold mb-4">Dashboard</h1>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-white p-4 rounded shadow">
        <h2 class="text-xl font-semibold mb-2">Quick Stats</h2>
        <ul>
            <li>Total Words: <?php echo $total_words; ?></li>
            <li>Words to Practice: <?php echo $words_to_practice; ?></li>
            <li>New Words: <?php echo $new_words; ?></li>
            <li>Overall Accuracy: <?php echo $accuracy; ?>%</li>
        </ul>
    </div>
    <div class="bg-white p-4 rounded shadow col-span-2 row-span-2">
        <h2 class="text-xl font-semibold mb-2">Number of Words by Accuracy</h2>
        <div class="flex flex-row">
            <?php $result = get_total_by_knowledge($pdo); ?>
            <p class="m-1 text-green-600">High: <?php echo $result['high']; ?></p>
            <p class="m-1 text-yellow-600">Average: <?php echo $result['mid']; ?></p>
            <p class="m-1 text-red-600">Low: <?php echo $result['low'] - $new_words; ?></p>
        </div>


        <div class="chart-container" style="position: relative; height:40vh; width:80vw">
            <canvas id="accuracyChart"></canvas>
        </div>
    </div>
    <div class="bg-white p-4 rounded shadow">
        <h2 class="text-xl font-semibold mb-2">Practice Session</h2>
        <a href="practice.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-gradient-to-r from-blue-600 to-yellow-600">Start Practice</a>
    </div>
</div>

<script>
    var ctx = document.getElementById('accuracyChart').getContext('2d');
    var accuracyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($dates_data); ?>, // X-axis dates
            datasets: [{
                    label: 'Total Words',
                    data: <?php echo json_encode($total_data); ?>,
                    borderColor: 'black',
                    fill: false,
                    tension: 0.4
                },{
                    label: 'High Accuracy (>80%)',
                    data: <?php echo json_encode($high_data); ?>,
                    borderColor: 'green',
                    fill: false,
                    tension: 0.4
                },
                {
                    label: 'Medium Accuracy (50-80%)',
                    data: <?php echo json_encode($medium_data); ?>,
                    borderColor: 'yellow',
                    fill: false,
                    tension: 0.4
                },
                {
                    label: 'Low Accuracy (<50%)',
                    data: <?php echo json_encode($low_data); ?>,
                    borderColor: 'red',
                    fill: false,
                    tension: 0.4
                }
            ]
        },
        options: {
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Number of Words'
                    }
                }
            }
        }
    });
</script>
<?php include 'footer.php'; ?>