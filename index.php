<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$city = isset($_GET['city']) ? $_GET['city'] : 'cesis,latvia';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>VTDT Sky</title>
  <link rel="stylesheet" href="styles.css?v=final">
</head>
<body>
  <header class="navbar">
  <!-- LEFT -->
  <div class="nav-left">
    <button class="menu-btn">☰</button>
    <h1 class="logo">VTDT Sky</h1>
    <div class="city-chip">
      <span>📍</span>
      <span id="cityName">Cēsis, LV</span>
    </div>
  </div>

  <!-- CENTER -->
  <div class="nav-center">
    <form id="searchForm" class="search-wrap" autocomplete="off">
      <span class="search-emoji">🔍</span>
      <input id="searchInput" type="text" placeholder="Search location..." value="<?= htmlspecialchars($city) ?>" />
      <button type="submit" class="globe-btn">🌐</button>
    </form>
  </div>

  <!-- RIGHT -->
  <div class="nav-right">
    <button id="themeToggle" class="theme-toggle">
      <span id="themeEmoji">🌞</span>
      <span id="themeText">Light</span>
    </button>

    <select id="unitSelect" class="unit-select">
  <option value="metric">Celsius and Kilometers</option>
  <option value="imperial">Fahrenheit and Miles</option>
</select>


    <!-- Bell (Notifications) -->
    <button class="icon-btn" aria-label="Notifications">
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor"
           stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell">
        <path d="M6 8a6 6 0 1 1 12 0c0 7 3 7 3 9H3c0-2 3-2 3-9Z"/>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
      </svg>
    </button>

    <!-- Settings (Gear) -->
    <button class="icon-btn" aria-label="Settings">
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor"
           stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings">
        <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06A1.65 1.65 0 0 0 15 19.4a1.65 1.65 0 0 0-1 .6 1.65 1.65 0 0 0-.33 1.82l.03.07a2 2 0 0 1-3.4 0l.03-.07a1.65 1.65 0 0 0-.33-1.82 1.65 1.65 0 0 0-1-.6 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-.6-1 1.65 1.65 0 0 0-1.82-.33l-.07.03a2 2 0 0 1 0-3.4l.07.03a1.65 1.65 0 0 0 1.82-.33 1.65 1.65 0 0 0 .6-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6c.37 0 .72-.13 1-.36.29-.25.5-.59.6-1l.03-.07a2 2 0 0 1 3.4 0l.03.07c.1.4.31.75.6 1 .28.23.63.36 1 .36.5 0 .97-.2 1.32-.55l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06c-.35.35-.55.82-.55 1.32 0 .37.13.72.36 1 .25.29.59.5 1 .6l.07.03a2 2 0 0 1 0 3.4l-.07-.03a1.65 1.65 0 0 0-1.82.33 1.65 1.65 0 0 0-.36 1Z"/>
      </svg>
    </button>
  </div>
</header>


  <main class="shell">
    <!-- LEFT COLUMN -->
    <section class="left-col">
      <article class="card main-card">
        <h3>Current Weather</h3>
        <p class="muted">Local time: <span id="localTime">--:--</span></p>

        <div class="current-row">
          <span id="currentIcon" class="wx-emoji">☁️</span>
          <span id="currentTemp" class="wx-temp">--°C</span>
          <div class="wx-desc">
            <div id="currentDesc" class="desc-main">--</div>
            <div class="muted">Feels Like <span id="feelsLike">--</span></div>
          </div>
        </div>

        <p class="muted">Current wind direction: <span id="windDir">--</span></p>
      </article>

      <div class="metrics-grid">
        <article class="card metric">
          <div class="metric-title">Air Quality</div>
          <div class="metric-value" id="airQ">2</div>
        </article>

        <article class="card metric">
          <div class="metric-title">Wind</div>
          <div class="metric-value"><span id="wind">--</span> km/h</div>
        </article>

        <article class="card metric">
          <div class="metric-title">Humidity</div>
          <div class="metric-value"><span id="humidity">--</span>%</div>
        </article>

        <article class="card metric">
          <div class="metric-title">Visibility</div>
          <div class="metric-value">10 km</div>
        </article>

        <article class="card metric">
          <div class="metric-title">Pressure</div>
          <div class="metric-value"><span id="pressIn">--</span> in</div>
        </article>

        <article class="card metric">
          <div class="metric-title">Pressure</div>
          <div class="metric-value"><span id="pressHpa">--</span> hPa</div>
        </article>
      </div>

      <!-- 🌞 SUN & 🌙 MOON SECTION -->
      <section class="card sunmoon-card">
        <h3>Sun & Moon Summary</h3>

        <!-- SUN -->
        <div class="sunmoon-row">
          <div class="left">
            <div class="sunmoon-label">
              <span class="sun-icon">☀️</span>
              <div>
                <div class="muted">Air Quality</div>
                <div id="airQSun">2</div>
              </div>
            </div>
          </div>

          <div class="center">
            <div class="astro">
              <img src="https://img.icons8.com/emoji/32/sunrise-emoji.png" alt="Sunrise" />
              <div class="muted">Sunrise</div>
              <div id="sunrise" class="astro-time">--:--</div>
            </div>

            <div class="gauge">
              <div class="gauge-fill" id="sunGauge"></div>
            </div>

            <div class="astro">
              <img src="https://img.icons8.com/emoji/32/sunset-emoji.png" alt="Sunset" />
              <div class="muted">Sunset</div>
              <div id="sunset" class="astro-time">--:--</div>
            </div>
          </div>
        </div>

        <!-- MOON -->
        <div class="sunmoon-row">
          <div class="left">
            <div class="sunmoon-label">
              <span class="moon-icon">🌙</span>
              <div>
                <div class="muted">Air Quality</div>
                <div id="airQMoon">2</div>
              </div>
            </div>
          </div>

          <div class="center">
            <div class="astro">
              <img src="https://img.icons8.com/emoji/32/first-quarter-moon-emoji.png" alt="Moonrise" />
              <div class="muted">Moonrise</div>
              <div id="moonrise" class="astro-time">--:--</div>
            </div>

            <div class="gauge">
              <div class="gauge-fill moon" id="moonGauge"></div>
            </div>

            <div class="astro">
              <img src="https://img.icons8.com/emoji/32/new-moon-emoji.png" alt="Moonset" />
              <div class="muted">Moonset</div>
              <div id="moonset" class="astro-time">--:--</div>
            </div>
          </div>
        </div>
      </section>
    </section>

    <!-- RIGHT COLUMN -->
    <aside class="right-col card">
      <div class="tabs">
        <button class="tab active" data-tab="today">Today</button>
        <button class="tab" data-tab="tomorrow">Tomorrow</button>
        <button class="tab" data-tab="tendays">10 Days</button>
      </div>

      <div id="tab-today" class="tab-body active">
        <div id="todayList" class="list"></div>
      </div>
      <div id="tab-tomorrow" class="tab-body">
        <div id="tomorrowList" class="list"></div>
      </div>
      <div id="tab-tendays" class="tab-body">
        <div id="tenDaysList" class="list"></div>
      </div>
    </aside>
  </main>

  <script src="script.js?v=final" defer></script>
</body>
</html>
