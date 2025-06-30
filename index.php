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
        </div>
        <div class="lg:col-span-2 flex flex-col space-y-6">
          <div class="glass p-6">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Analysis Result</h3>
            <div id="footballResult" class="text-gray-800 dark:text-gray-200 space-y-4 p-4 bg-gray-100/50 dark:bg-gray-700/30 rounded-lg transition-all duration-200"></div>
            <button onclick="saveFootballMatch()" class="mt-4 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold transform hover:scale-105 transition-all duration-200 shadow">
              <i class="fas fa-save mr-2"></i> Save
            </button>
          </div>
          <div class="glass p-6">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Match History</h3>
            <div id="savedFootballMatches" class="space-y-4"></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- === BASKETBALL === -->
  <section id="basketball" class="section hidden animate__animated animate__fadeIn">
    <div class="glass p-8 rounded-2xl shadow-xl mb-10">
      <h2 class="text-3xl font-extrabold mb-3 text-basketball-primary dark:text-basketball-accent flex items-center gap-2">
        <i class="fas fa-basketball-ball"></i> Basketball Match Analysis
      </h2>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="space-y-6">
          <div>
            <label class="font-semibold block mb-1">Match Name</label>
            <input type="text" id="basketballMatchName" placeholder="Team A vs Team B" class="w-full p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"/>
            <label class="font-semibold block mt-4 mb-1">Match Date</label>
            <input type="date" id="basketballMatchDate" class="w-full p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"/>
          </div>
          <div>
            <label class="font-semibold block mb-1">Reference Total</label>
            <input type="number" id="referansTotal" placeholder="225.0" class="w-full p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200"/>
          </div>
          <div>
            <label class="font-semibold block mb-1">Last 3 Home/Away Scores</label>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <input type="number" id="basketballHome1" placeholder="Home 1" class="w-full mb-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border"/>
                <input type="number" id="basketballHome2" placeholder="Home 2" class="w-full mb-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border"/>
                <input type="number" id="basketballHome3" placeholder="Home 3" class="w-full p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border"/>
                <input type="number" id="basketballLineHome" placeholder="Home Line" class="w-full mt-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border"/>
              </div>
              <div>
                <input type="number" id="basketballAway1" placeholder="Away 1" class="w-full mb-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border"/>
                <input type="number" id="basketballAway2" placeholder="Away 2" class="w-full mb-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border"/>
                <input type="number" id="basketballAway3" placeholder="Away 3" class="w-full p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border"/>
                <input type="number" id="basketballLineAway" placeholder="Away Line" class="w-full mt-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border"/>
              </div>
            </div>
            <label class="font-semibold block mt-4 mb-1">Match Line</label>
            <input type="number" id="basketballMatchLine" placeholder="223.5" class="w-full p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border"/>
          </div>
          <button onclick="analyzeBasketball()" class="w-full mt-4 bg-basketball-primary hover:bg-basketball-accent dark:bg-basketball-accent dark:hover:bg-basketball-primary text-white px-6 py-3 rounded-xl font-semibold transform hover:scale-105 transition-all duration-200 shadow">
            <i class="fas fa-calculator mr-2"></i> Analyze
          </button>
        </div>
        <div class="lg:col-span-2 flex flex-col space-y-6">
          <div class="glass p-6">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Analysis Results</h3>
            <div class="space-y-3 text-gray-800 dark:text-gray-200">
              <p>Home Estimate: <span id="basketballHomeEst" class="font-semibold"></span></p>
              <p>Away Estimate: <span id="basketballAwayEst" class="font-semibold"></span></p>
              <p>Our Total: <span id="basketballTotalEst" class="font-semibold"></span></p>
              <p>Reference Total: <span id="basketballRefEst" class="font-semibold"></span></p>
              <p>Combined Total: <span id="basketballCombinedEst" class="font-semibold"></span></p>
              <p>Match Line: <span id="basketballMatchLineOut" class="font-semibold"></span></p>
            </div>
            <div class="mt-6 space-y-2">
              <p id="basketballMatchLineVerdict" class="text-lg"></p>
              <p id="basketballHomeVerdict" class="text-lg"></p>
              <p id="basketballAwayVerdict" class="text-lg"></p>
            </div>
            <button onclick="saveBasketballMatch()" class="mt-4 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold transform hover:scale-105 transition-all duration-200 shadow">
              <i class="fas fa-save mr-2"></i> Save
            </button>
          </div>
          <div class="glass p-6">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Match History</h3>
            <div id="savedBasketballMatches" class="space-y-4"></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- === NBA === -->
  <section id="nba" class="section hidden animate__animated animate__fadeIn">
    <div class="glass p-8 rounded-2xl shadow-xl mb-10">
      <h2 class="text-3xl font-extrabold mb-3 text-nba-primary dark:text-nba-accent flex items-center gap-2">
        <i class="fas fa-trophy"></i> NBA Match Analysis
      </h2>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="space-y-6">
          <div>
            <label class="font-semibold block mb-1">Match Name</label>
            <input type="text" id="nbaMatchName" placeholder="NBA Teams" class="w-full p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border"/>
            <label class="font-semibold block mt-4 mb-1">Match Date</label>
            <input type="date" id="nbaMatchDate" class="w-full p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border"/>
          </div>
          <div>
            <label class="font-semibold block mb-1">Last 3 Home/Away Scores</label>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <input type="number" id="nbaHome1" placeholder="Home 1" class="w-full mb-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border"/>
                <input type="number" id="nbaHome2" placeholder="Home 2" class="w-full mb-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border"/>
                <input type="number" id="nbaHome3" placeholder="Home 3" class="w-full p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border"/>
                <input type="number" id="nbaLineHome" placeholder="Home Line" class="w-full mt-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border"/>
              </div>
              <div>
                <input type="number" id="nbaAway1" placeholder="Away 1" class="w-full mb-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border"/>
                <input type="number" id="nbaAway2" placeholder="Away 2" class="w-full mb-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border"/>
                <input type="number" id="nbaAway3" placeholder="Away 3" class="w-full p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border"/>
                <input type="number" id="nbaLineAway" placeholder="Away Line" class="w-full mt-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border"/>
              </div>
            </div>
            <label class="font-semibold block mt-4 mb-1">Match Line</label>
            <input type="number" id="nbaMatchLine" placeholder="223.5" class="w-full p-3 rounded-lg bg-gray-50 dark:bg-gray-700 border"/>
          </div>
          <button onclick="analyzeNBA()" class="w-full mt-4 bg-nba-primary hover:bg-nba-accent dark:bg-nba-accent dark:hover:bg-nba-primary text-white px-6 py-3 rounded-xl font-semibold transform hover:scale-105 transition-all duration-200 shadow">
            <i class="fas fa-calculator mr-2"></i> Analyze
          </button>
        </div>
        <div class="lg:col-span-2 flex flex-col space-y-6">
          <div class="glass p-6">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Analysis Results</h3>
            <div class="space-y-3 text-gray-800 dark:text-gray-200">
              <p>Home Estimate: <span id="nbaHomeEst" class="font-semibold"></span></p>
              <p>Away Estimate: <span id="nbaAwayEst" class="font-semibold"></span></p>
              <p>Total Points: <span id="nbaTotalEst" class="font-semibold"></span></p>
              <p>Match Line: <span id="nbaMatchLineOut" class="font-semibold"></span></p>
            </div>
            <div class="mt-6 space-y-2">
              <p id="nbaMatchLineVerdict" class="text-lg"></p>
              <p id="nbaHomeVerdict" class="text-lg"></p>
              <p id="nbaAwayVerdict" class="text-lg"></p>
            </div>
            <button onclick="saveNBAMatch()" class="mt-4 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold transform hover:scale-105 transition-all duration-200 shadow">
              <i class="fas fa-save mr-2"></i> Save
            </button>
          </div>
          <div class="glass p-6">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Match History</h3>
            <div id="savedNBAMatches" class="space-y-4"></div>
          </div>
        </div>
      </div>
    </div>
  </section>
<?php
require_once 'footer.php';
?>