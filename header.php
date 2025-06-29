<?php
session_start();
// Hataları göster
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'dbCon.php';
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Harbigol - Pro Sports Analytics</title>
    <meta name="description" content="Latest AI-powered football, basketball & NBA predictions, advanced xG stats, betting tips and daily analysis. Download now!" />
    <link rel="icon" type="image/png" href=""/>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>
    <!-- AdSense -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2865767138536274" crossorigin="anonymous"></script>
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- Google News Showcase SWG Scripts -->
    <script async type="application/javascript" src="https://news.google.com/swg/js/v1/swg-basic.js"></script>
    <script>
        (self.SWG_BASIC = self.SWG_BASIC || []).push( basicSubscriptions => {
            basicSubscriptions.init({
                type: "NewsArticle",
                isPartOfType: ["Product"],
                isPartOfProductId: "CAowxfmLCw:openaccess",
                clientOptions: { theme: "light", lang: "en" },
            });
        });
    </script>
    <script async type="application/javascript" src="https://news.google.com/swg/js/v1/swg-basic.js"></script>
    <script>
        (self.SWG_BASIC = self.SWG_BASIC || []).push( basicSubscriptions => {
            basicSubscriptions.init({
                type: "NewsArticle",
                isPartOfType: ["Product"],
                isPartOfProductId: "CAowxfmLCw:Monthly",
                clientOptions: { theme: "light", lang: "en" },
            });
        });
    </script>
    <style>
        html { scroll-behavior: smooth; }
        .glass { background: rgba(255,255,255,0.13); backdrop-filter: blur(18px); border: 1px solid rgba(255,255,255,0.19); }
        .dark .glass { background: rgba(16,18,22,0.55); border-color: rgba(200,200,200,0.09);}
        ::-webkit-scrollbar { width: 9px; }
        ::-webkit-scrollbar-thumb { background: #888; border-radius: 5px; }
        ::-webkit-scrollbar-thumb:hover { background: #fbbf24; }
        .dark::-webkit-scrollbar-thumb { background: #3b82f6; }
        .adsense-banner { margin: 24px 0; border-radius: 12px; overflow: hidden;}
        #promoModal {display:none; position:fixed; z-index:10001; inset:0; background:rgba(18,19,21,0.85); align-items:center; justify-content:center;}
        #promoModal .modal-inner { max-width:95vw; width:430px; background:#fff; border-radius:1.5rem; box-shadow:0 10px 44px #0002; padding:2.5rem 2rem 2rem 2rem; position:relative; text-align:center; overflow:hidden;}
        #promoModal .close-btn { position:absolute; top:1rem; right:1rem; background:#fbbf24; border:none; border-radius:50%; width:36px; height:36px; font-size:1.25rem; color:#222; cursor:pointer; }
        #promoModal .close-btn:hover {background:#fde68a;}
        .store-btn {display:flex;align-items:center;justify-content:center;gap:9px;width:170px;padding:12px 0;margin:10px 0;border-radius:11px;font-size:1.11rem;font-weight:600;transition:all .22s;}
        .store-btn--google {background: linear-gradient(90deg,#3973f6 0%,#fed935 100%);color:#fff;}
        .store-btn--google:hover{background:linear-gradient(90deg,#2258bf 0%,#fee482 100%);}
        .store-btn--apple {background: #23272f;color:#fff;}
        .store-btn--apple:hover{background:#444;}
        @media (max-width: 500px) {
            .store-btn {width:100%;min-width:0;}
        }
    </style>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        football: { light: '#EAFAEA', dark: '#162A16', primary: '#2E7D32', accent: '#81C784' },
                        basketball: { light: '#FFF8E1', dark: '#4E2A10', primary: '#EF6C00', accent: '#FFB74D' },
                        nba: { light: '#E3F2FD', dark: '#0A1A2A', primary: '#1E3A8A', accent: '#3B82F6' }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">

<!-- NAVBAR -->
<nav class="sticky top-0 z-40 backdrop-blur-xl bg-white/90 dark:bg-gray-900/80 shadow-md border-b border-gray-100 dark:border-gray-800 transition-all duration-300 animate__animated animate__slideInDown">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a class="flex items-center gap-3" href="/">
                <img src="https://www.harbigol.com/wp-content/uploads/2025/06/Sari-Tipografik-Medya-Sirketi-Logosu.png" class="w-12 h-12 rounded-full border-2 border-yellow-400 shadow" alt="Logo">
                <span class="text-2xl font-bold bg-gradient-to-r from-yellow-400 to-blue-500 text-transparent bg-clip-text animate__animated animate__pulse animate__infinite">Harbigol</span>
            </a>
            <div class="flex gap-3 items-center">
                <button onclick="showSection('football')" class="nav-btn px-3 py-2 rounded-lg font-medium text-football-primary dark:text-football-accent hover:bg-football-primary/10 dark:hover:bg-football-accent/20 transition">
                    <i class="fas fa-futbol mr-1"></i>Football
                </button>
                <button onclick="showSection('basketball')" class="nav-btn px-3 py-2 rounded-lg font-medium text-basketball-primary dark:text-basketball-accent hover:bg-basketball-primary/10 dark:hover:bg-basketball-accent/20 transition">
                    <i class="fas fa-basketball-ball mr-1"></i>Basketball
                </button>
                <button onclick="showSection('nba')" class="nav-btn px-3 py-2 rounded-lg font-medium text-nba-primary dark:text-nba-accent hover:bg-nba-primary/10 dark:hover:bg-nba-accent/20 transition">
                    <i class="fas fa-trophy mr-1"></i>NBA
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- HERO -->
<div class="w-full flex flex-col items-center bg-gradient-to-br from-yellow-50 via-blue-50 to-white dark:from-blue-950 dark:via-gray-900 dark:to-gray-800 py-12 mb-8 animate__animated animate__fadeIn">
    <h1 class="text-4xl md:text-6xl font-extrabold mb-4 bg-gradient-to-r from-yellow-500 to-blue-700 text-transparent bg-clip-text drop-shadow-xl">AI-Driven Football & Basketball Analysis</h1>
    <p class="text-lg md:text-2xl text-gray-700 dark:text-gray-200 mb-5">Daily predictions powered by advanced stats & xG models. <span class="text-yellow-500 font-bold">Boost your betting success with Harbigol!</span></p>
</div>

<!-- MAIN (SECTIONS) -->
<main class="max-w-7xl mx-auto px-4 py-8 space-y-12">