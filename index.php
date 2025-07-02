<?php
// Hataları göster
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// DB bağlantısı
$dsn = 'mysql:host=localhost;dbname=bigol;charset=utf8mb4';
$dbUser = 'root';
$dbPass = '';
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// AJAX işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $password = $_POST['password'] ?? '';

    if (in_array($action, ['save_football', 'save_basketball', 'save_nba', 'delete_match']) && $password !== '3005') {
        echo json_encode(['success' => false, 'message' => 'Wrong password!']);
        exit;
    }

    switch ($action) {
        case 'save_football':
            try {
                $stmt = $pdo->prepare("INSERT INTO football_matches (name, match_date, score1, score2, score3, home_xg, home_xga, away_xg, away_xga, result, best_bet, best_conf, second_bet, second_conf) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['date'],
                    $_POST['score1'],
                    $_POST['score2'],
                    $_POST['score3'],
                    $_POST['home_xg'],
                    $_POST['home_xga'],
                    $_POST['away_xg'],
                    $_POST['away_xga'],
                    $_POST['result'],
                    $_POST['best_bet'],
                    $_POST['best_conf'],
                    $_POST['second_bet'],
                    $_POST['second_conf']
                ]);
                echo json_encode(['success' => true, 'message' => 'Football match saved!']);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
            break;

        case 'save_basketball':
            try {
                $stmt = $pdo->prepare("INSERT INTO basketball_matches (name, match_date, home1, home2, home3, line_home, away1, away2, away3, line_away, match_line, verdict_match, verdict_home, verdict_away) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['date'],
                    $_POST['home1'],
                    $_POST['home2'],
                    $_POST['home3'],
                    $_POST['line_home'],
                    $_POST['away1'],
                    $_POST['away2'],
                    $_POST['away3'],
                    $_POST['line_away'],
                    $_POST['match_line'],
                    $_POST['verdict_match'],
                    $_POST['verdict_home'],
                    $_POST['verdict_away']
                ]);
                echo json_encode(['success' => true, 'message' => 'Basketball match saved!']);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
            break;

        case 'save_nba':
            try {
                $stmt = $pdo->prepare("INSERT INTO nba_matches (name, match_date, home1, home2, home3, line_home, away1, away2, away3, line_away, match_line, verdict_match, verdict_home, verdict_away) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['date'],
                    $_POST['home1'],
                    $_POST['home2'],
                    $_POST['home3'],
                    $_POST['line_home'],
                    $_POST['away1'],
                    $_POST['away2'],
                    $_POST['away3'],
                    $_POST['line_away'],
                    $_POST['match_line'],
                    $_POST['verdict_match'],
                    $_POST['verdict_home'],
                    $_POST['verdict_away']
                ]);
                echo json_encode(['success' => true, 'message' => 'NBA match saved!']);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
            break;

        case 'delete_match':
            try {
                $table = $_POST['table'];
                $id = $_POST['id'];
                $allowedTables = ['football_matches', 'basketball_matches', 'nba_matches'];
                if (in_array($table, $allowedTables)) {
                    $stmt = $pdo->prepare("DELETE FROM {$table} WHERE id = ?");
                    $stmt->execute([$id]);
                    echo json_encode(['success' => true, 'message' => 'Match deleted!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid table!']);
                }
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
            break;

        case 'load_matches':
            try {
                $type = $_POST['type'];
                $table = '';
                switch ($type) {
                    case 'football':
                        $table = 'football_matches';
                        break;
                    case 'basketball':
                        $table = 'basketball_matches';
                        break;
                    case 'nba':
                        $table = 'nba_matches';
                        break;
                    default:
                        echo json_encode(['success' => false, 'message' => 'Invalid type!']);
                        exit;
                }
                $stmt = $pdo->prepare("SELECT * FROM {$table} ORDER BY created_at DESC LIMIT 35");
                $stmt->execute();
                $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'matches' => $matches]);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
            break;
    }
    exit;
}

require_once 'header.php';
require_once 'component.php';
?>


  <!-- === FOOTBALL === -->
  <section id="football" class="section animate__animated animate__fadeIn">
    <div class="glass p-8 rounded-2xl shadow-xl mb-10">
      <h2 class="text-3xl font-extrabold mb-3 text-football-primary dark:text-football-accent flex items-center gap-2">
        <i class="fas fa-futbol"></i> Football Match Analysis
      </h2>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Inputs -->
        <div class="space-y-6">
          <div>
            <label class="font-semibold block mb-1">Match Name</label>
            <input type="text" id="footballMatchName" placeholder="Team A vs Team B" class="w-full p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"/>
            <label class="font-semibold block mt-4 mb-1">Match Date</label>
            <input type="date" id="footballMatchDate" class="w-full p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"/>
          </div>
          <div>
            <label class="font-semibold block mb-1">Last 3 Match Scores (format: 2-1, 1-1, ...)</label>
            <input type="text" id="score1" placeholder="2-1" class="w-full p-3 mb-2 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"/>
            <input type="text" id="score2" placeholder="1-0" class="w-full p-3 mb-2 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"/>
            <input type="text" id="score3" placeholder="3-1" class="w-full p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"/>
          </div>
          <div>
            <label class="font-semibold block mb-1">Expected Goals (xG / xGA)</label>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label>Home xG</label>
                <input type="number" id="homeXG" step="0.01" placeholder="1.82" class="w-full p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"/>
                <label class="mt-2 block">Home xGA</label>
                <input type="number" id="homeXGA" step="0.01" placeholder="0.95" class="w-full p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"/>
              </div>
              <div>
                <label>Away xG</label>
                <input type="number" id="awayXG" step="0.01" placeholder="1.45" class="w-full p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"/>
                <label class="mt-2 block">Away xGA</label>
                <input type="number" id="awayXGA" step="0.01" placeholder="1.25" class="w-full p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"/>
              </div>
            </div>
          </div>
          <button onclick="analyzeFootball()" class="w-full mt-4 bg-football-primary hover:bg-football-accent text-white px-6 py-3 rounded-xl font-semibold transform hover:scale-105 transition-all duration-200 shadow">
            <i class="fas fa-calculator mr-2"></i> Analyze
          </button>

            <!-- Left Area Moved-->
            <div class="glass p-6">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Analysis Result</h3>
                <div id="footballResult" class="text-gray-800 dark:text-gray-200 space-y-4 p-4 bg-gray-100/50 dark:bg-gray-700/30 rounded-lg transition-all duration-200"></div>


                <?php
                // Admin Role View Save Button Football
                @$role = $_SESSION['role'];

                if ($role == 1) {
                    echo '
                  <button onclick="saveFootballMatch()" class="mt-4 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold transform hover:scale-105 transition-all duration-200 shadow">
              <i class="fas fa-save mr-2"></i> Save
            </button>
                  ';

                }


                ?>


            </div>

            <!-- Left Area Moved-->



        </div>



        <div class="lg:col-span-2 flex flex-col space-y-6">
            <?php
            // Football Matches Results
            @$email = $_SESSION['email'];
            @$ibanConfirm = $_SESSION['ibanConfirm'];
            if ($email) {
                if ($ibanConfirm == 1) {
                    echo '<div class="glass p-6">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Match History</h3>
            <div id="savedFootballMatches" class="space-y-4"></div>
          </div>';
                }
                if($ibanConfirm == 0) {
                    echo '<div class="px-4 py-2 bg-amber-500 text-white rounded">
                Kayıtlı maçları görüntülemek için üyeliğinizi premiuma yükseltin. <a href="profile.php" class="font-bold underline">Profil Sayfam.</a>
                </div>';
                }
            }else {
                echo loginCard();
            }


            $page = isset($_GET['page']) ? $_GET['page'] : 'default';
            ?>
            <div class="w-full">
                <div class="flex flex-row flex-wrap items-center justify-between">
                    <a href="?page=skor1" class="border bg-white rounded px-4 py-2">Yapay Zeka Skor 1</a>
                    <a href="?page=skor2" class="border bg-white rounded px-4 py-2">Yapay Zeka Skor 2</a>
                    <a href="?page=skor3" class="border bg-white rounded px-4 py-2">Yapay Zeka Skor 3</a>
                </div>
            </div>

            <?php

            switch ($page) {
                case 'skor2';
                    include 'skor2.php';
                    break;
                case 'skor3';
                    include 'test.php';
                    break;

                default;
                    include 'skor1.php';
                    break;
            }

            ?>







        </div>
      </div>
    </div>
  </section>

<?php
require_once 'footer.php';
?>