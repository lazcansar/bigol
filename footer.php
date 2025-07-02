
</main>

<script>
    // --- Yardımcı Fonksiyonlar ---
    function parseScore(score) {
        if (!score) return { home: 0, away: 0 };
        const parts = score.split('-').map(n => parseInt(n.trim()));
        return { home: parts[0] || 0, away: parts[1] || 0 };
    }
    function poissonProb(lambda, k) {
        return Math.pow(lambda, k) * Math.exp(-lambda) / factorial(k);
    }
    function factorial(n) {
        return n === 0 ? 1 : n * factorial(n - 1);
    }

    // --- AJAX ---
    async function sendRequest(action, data) {
        const formData = new FormData();
        formData.append('action', action);
        for (const key in data) formData.append(key, data[key]);
        try {
            const response = await fetch('', { method: 'POST', body: formData });
            return await response.json();
        } catch (error) {
            console.error('Request failed:', error);
            return { success: false, message: 'Request failed' };
        }
    }

    // --- Sekme Geçişi ---
    function showSection(sec) {
        document.querySelectorAll('.section').forEach(el => el.classList.add('hidden'));
        const sel = document.getElementById(sec);
        if(sel) sel.classList.remove('hidden');
    }

    // --- Tema ---
    function toggleDarkMode() {
        document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme',document.documentElement.classList.contains('dark')?'dark':'light');
    }

    // --- Maç Yükleme ---
    async function loadMatches(type) {
        const result = await sendRequest('load_matches', { type });
        if (result.success) displayMatches(type, result.matches);
    }
    function displayMatches(type, matches) {
        const containerId = type === 'football' ? 'savedFootballMatches' :
            type === 'basketball' ? 'savedBasketballMatches' : 'savedNBAMatches';
        const container = document.getElementById(containerId);
        container.innerHTML = matches.map(match => {
            let content = `
      <div class="glass p-4 rounded-lg hover:bg-gray-100/10 dark:hover:bg-gray-700/30 transition-all duration-200 transform hover:scale-[1.01]">
        <div class="flex items-center justify-between mb-2">
          <div class="flex flex-col sm:flex-row sm:items-center gap-2">
            <span class="font-semibold text-gray-800 dark:text-gray-100">${match.name || 'Unnamed Match'}</span>
            <span class="text-gray-500 dark:text-gray-300 text-sm">${match.match_date || 'No date'}</span>
          </div>

              <?php
// Admin Role View Save Button Football
@$role = $_SESSION['role'];

if ($role == 1) {
    echo '
    <button onclick="deleteMatch(\'${type}\', ${match.id})" class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
            <i class="fas fa-trash"></i>
          </button>
    ';
    }
?>

        </div>
        <div class="text-sm">`;
            if (type === 'football') {
                content += `<div class="text-blue-600 dark:text-blue-400"><b>Best:</b> ${match.best_bet || '-'} <b>(${match.best_conf || '-'})</b> <br>
                    <b>Second:</b> ${match.second_bet || '-'} <b>(${match.second_conf || '-'})</b></div>`;
            } else {
                const playableVerdicts = [match.verdict_match, match.verdict_home, match.verdict_away]
                    .filter(v => v && !v.includes('NOT RECOMMENDED'));
                content += playableVerdicts.length > 0 ?
                    `<div class="font-semibold text-green-600 dark:text-green-400">${playableVerdicts[0]}</div>` :
                    '<span class="text-gray-500 dark:text-gray-400">No playable predictions</span>';
            }
            content += `</div></div>`;
            return content;
        }).join('');
    }

    // --- Silme ---
    async function deleteMatch(type, id) {
        const pwd = prompt('Please enter the delete password:');
        if (pwd === '3005') {
            const table = type + '_matches';
            const result = await sendRequest('delete_match', { password: pwd, table, id });
            if (result.success) loadMatches(type);
            else alert(result.message);
        } else alert('Wrong password!');
    }

    // === FOOTBALL ANALYSIS & SAVE ===
    function analyzeFootball() {
        const scores = [
            parseScore(document.getElementById('score1').value),
            parseScore(document.getElementById('score2').value),
            parseScore(document.getElementById('score3').value),
        ];
        const homeXG = parseFloat(document.getElementById('homeXG').value);
        const homeXGA = parseFloat(document.getElementById('homeXGA').value);
        const awayXG = parseFloat(document.getElementById('awayXG').value);
        const awayXGA = parseFloat(document.getElementById('awayXGA').value);

        if ([homeXG, homeXGA, awayXG, awayXGA].some(isNaN)) {
            alert('Please fill all xG fields.');
            return;
        }
        const avgHome = (homeXG + awayXGA) / 2;
        const avgAway = (awayXG + homeXGA) / 2;

        const bets = {
            'Over 2.5': 0, 'Under 2.5': 0, 'BTTS Yes': 0, 'BTTS No': 0,
            'Home Over 1.5': 0, 'Home Under 1.5': 0, 'Away Over 1.5': 0, 'Away Under 1.5': 0,
        };
        scores.forEach((s) => {
            const tot = s.home + s.away;
            if (tot > 2.5) bets['Over 2.5']++; else bets['Under 2.5']++;
            if (s.home > 0 && s.away > 0) bets['BTTS Yes']++; else bets['BTTS No']++;
            if (s.home > 1.5) bets['Home Over 1.5']++; else bets['Home Under 1.5']++;
            if (s.away > 1.5) bets['Away Over 1.5']++; else bets['Away Under 1.5']++;
        });

        const poisson = {
            'Over 2.5': 0, 'Under 2.5': 0, 'BTTS Yes': 0, 'BTTS No': 0,
            'Home Over 1.5': 0, 'Home Under 1.5': 0, 'Away Over 1.5': 0, 'Away Under 1.5': 0,
        };
        for (let h = 0; h <= 5; h++) {
            for (let a = 0; a <= 5; a++) {
                const p = poissonProb(avgHome, h) * poissonProb(avgAway, a);
                const tot = h + a;
                if (tot > 2.5) poisson['Over 2.5'] += p; else poisson['Under 2.5'] += p;
                if (h > 0 && a > 0) poisson['BTTS Yes'] += p; else poisson['BTTS No'] += p;
                if (h > 1.5) poisson['Home Over 1.5'] += p; else poisson['Home Under 1.5'] += p;
                if (a > 1.5) poisson['Away Over 1.5'] += p; else poisson['Away Under 1.5'] += p;
            }
        }
        // Nihai skorlar (karar fonksiyonu)
        const combined = {};
        Object.keys(bets).forEach((k) => (combined[k] = bets[k] / 3 * 0.33 + poisson[k] * 0.67));
        // En yüksek iki tahmin ve oranları
        let allBets = Object.entries(combined).map(([bet, val]) => ({ bet, val }));
        allBets.sort((a, b) => b.val - a.val);
        let best = allBets[0], second = allBets[1];

        // Sonuç çıktısı
        document.getElementById('footballResult').innerHTML = `
      <div class="animate__animated animate__fadeIn space-y-4">
        <div class="flex items-center space-x-2 text-football-primary dark:text-football-accent text-xl font-semibold">
          <i class="fas fa-chart-bar"></i><span>Analysis Result</span>
        </div>
        <div class="p-4 bg-football-light dark:bg-football-dark rounded-lg">
          <p class="text-football-primary dark:text-football-accent">
            <span class="font-semibold">Best Prediction:</span>
            <span class="ml-2">${best.bet}</span>
            <span class="ml-2">(${(best.val * 100).toFixed(1)}%)</span>
          </p>
          <p class="text-football-primary dark:text-football-accent">
            <span class="font-semibold">Second Best:</span>
            <span class="ml-2">${second.bet}</span>
            <span class="ml-2">(${(second.val * 100).toFixed(1)}%)</span>
          </p>
        </div>
      </div>`;
        // Best ve Second prediction'ı kaydetmek için inputlara da yazalım
        window._best_bet = best.bet;
        window._best_conf = (best.val * 100).toFixed(1) + "%";
        window._second_bet = second.bet;
        window._second_conf = (second.val * 100).toFixed(1) + "%";
    }

    async function saveFootballMatch() {
        const pwd = prompt('Please enter the save password:');
        if (pwd === '3005') {
            const data = {
                password: pwd,
                name: document.getElementById('footballMatchName').value || 'Unnamed Match',
                date: document.getElementById('footballMatchDate').value || new Date().toISOString().split('T')[0],
                score1: document.getElementById('score1').value,
                score2: document.getElementById('score2').value,
                score3: document.getElementById('score3').value,
                home_xg: document.getElementById('homeXG').value,
                home_xga: document.getElementById('homeXGA').value,
                away_xg: document.getElementById('awayXG').value,
                away_xga: document.getElementById('awayXGA').value,
                result: document.getElementById('footballResult').innerHTML,
                best_bet: window._best_bet || '',
                best_conf: window._best_conf || '',
                second_bet: window._second_bet || '',
                second_conf: window._second_conf || ''
            };
            const result = await sendRequest('save_football', data);
            if (result.success) {
                alert(result.message);
                loadMatches('football');
            } else alert(result.message);
        } else alert('Wrong password!');
    }


    // --- Sayfa Yüklenince ---
    window.addEventListener('DOMContentLoaded', function () {
        loadMatches('football');
        if(localStorage.getItem('theme')==='dark') document.documentElement.classList.add('dark');
    });
</script>

</body>
</html>
