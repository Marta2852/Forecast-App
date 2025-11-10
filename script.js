const $ = s => document.querySelector(s);

// --- Time formatters (FIXED: lock formatter to UTC after adding city offset) ---
const fmtTime = (ts,tz) =>
  new Date((ts + tz) * 1000).toLocaleTimeString([], { hour:'2-digit', minute:'2-digit', timeZone:'UTC' });

const fmtHour = (ts,tz) =>
  new Date((ts + tz) * 1000).toLocaleTimeString([], { hour:'numeric', minute:'2-digit', timeZone:'UTC' });

const fmtDay  = (ts,tz) =>
  new Date((ts + tz) * 1000).toLocaleDateString([], { weekday:'long', timeZone:'UTC' });

// Current city time “now”
const cityNow = tz => {
  const nowUtcSec = Math.floor(Date.now()/1000);
  return fmtTime(nowUtcSec, tz);
};

// --- Icons & helpers ---
const iconFrom = d => {
  d = (d||"").toLowerCase();
  if(d.includes('snow')) return '❄️';
  if(d.includes('rain')) return '🌧️';
  if(d.includes('cloud')) return '☁️';
  if(d.includes('clear')) return '☀️';
  if(d.includes('mist')||d.includes('fog')) return '🌫️';
  return '🌤️';
};
const dirFromDeg = d => (['N','NE','E','SE','S','SW','W','NW'][Math.round(d/45)%8]);

// --- Unit toggle (C/F and km/h ↔ mph) ---
const unitSelect = $('#unitSelect');
let currentUnit = localStorage.getItem('unit') || 'metric'; // 'metric' or 'imperial'
if (unitSelect) unitSelect.value = currentUnit;

function convertTemp(c) { return currentUnit === 'imperial' ? (c * 9/5 + 32) : c; }
function convertSpeed(km) { return currentUnit === 'imperial' ? (km / 1.609) : km; }
function tempUnit() { return currentUnit === 'imperial' ? '°F' : '°C'; }
function speedUnit() { return currentUnit === 'imperial' ? 'mph' : 'km/h'; }

if (unitSelect) {
  unitSelect.addEventListener('change', () => {
    currentUnit = unitSelect.value;
    localStorage.setItem('unit', currentUnit);
    load($('#searchInput').value || 'cesis,latvia');
  });
}

// --- Theme toggle ---
const themeToggle = $('#themeToggle');
function setTheme(t){
  document.body.classList.toggle('dark', t==='dark');
  $('#themeEmoji').textContent = t==='dark'?'🌙':'🌞';
  $('#themeText').textContent = t==='dark'?'Dark':'Light';
  localStorage.setItem('theme',t);
}
setTheme(localStorage.getItem('theme')||'light');
if (themeToggle) themeToggle.onclick=()=>setTheme(document.body.classList.contains('dark')?'light':'dark');

// --- Tabs ---
document.querySelectorAll('.tab').forEach(btn=>{
  btn.addEventListener('click',()=>{
    document.querySelectorAll('.tab, .tab-body').forEach(el=>el.classList.remove('active'));
    btn.classList.add('active');
    $('#tab-'+btn.dataset.tab).classList.add('active');
  });
});

// --- Main load ---
async function load(city){
  try{
    const res = await fetch('api.php?city='+encodeURIComponent(city));
    const data = await res.json();
    if(!data.list) return alert('No data for this city');

    const tz = data.city.timezone || 0; // seconds offset from UTC
    const d0 = data.list[0];

    $('#cityName').textContent = data.city.name;
    $('#localTime').textContent = cityNow(tz);  // ✅ fixed city local time
    $('#currentIcon').textContent = iconFrom(d0.weather[0].main);
    $('#currentTemp').textContent = `${convertTemp(d0.temp.day).toFixed(1)}${tempUnit()}`;
    $('#currentDesc').textContent = d0.weather[0].description;
    $('#feelsLike').textContent = `${convertTemp(d0.feels_like.day).toFixed(1)}${tempUnit()}`;
    $('#wind').textContent = `${convertSpeed(d0.speed).toFixed(1)} ${speedUnit()}`;
    $('#windDir').textContent = dirFromDeg(d0.deg);
    $('#humidity').textContent = d0.humidity;
    $('#pressIn').textContent = (d0.pressure * 0.02953).toFixed(2);
    $('#pressHpa').textContent = d0.pressure;

    // Sun / Moon (times come as UTC seconds; we add tz and format in UTC)
    const sr = d0.sunrise, ss = d0.sunset;
    const now = Math.floor(Date.now()/1000);
    $('#sunrise').textContent = fmtTime(sr,tz);
    $('#sunset').textContent  = fmtTime(ss,tz);
    const sunProgress = Math.max(0, Math.min(1, (now - sr)/(ss - sr)));
    $('#sunGauge').style.transform = `rotate(${sunProgress*180}deg)`;

    // Fake moonrise/set based on sunrise/sunset
    const mr = sr + 21600; // +6h
    const ms = ss + 43200; // +12h
    $('#moonrise').textContent = fmtTime(mr,tz);
    $('#moonset').textContent  = fmtTime(ms,tz);
    const moonProgress = Math.max(0, Math.min(1, (now - mr)/(ms - mr)));
    $('#moonGauge').style.transform = `rotate(${moonProgress*180}deg)`;

    // Lists
    const todayList=$('#todayList'), tomorrowList=$('#tomorrowList'), tenDaysList=$('#tenDaysList');
    todayList.innerHTML = tomorrowList.innerHTML = tenDaysList.innerHTML = '';

    for(let i=0;i<12;i++){
      todayList.appendChild(row(
        fmtHour(d0.dt + i*3600, tz),
        d0.weather[0].description,
        d0.weather[0].main,
        `${convertTemp(d0.temp.day - i*0.1).toFixed(1)}${tempUnit()}`,
        `${convertSpeed(d0.speed).toFixed(1)} ${speedUnit()}`,
        `${d0.humidity}%`
      ));
    }

    const d1 = data.list[1];
    for(let i=0;i<12;i++){
      tomorrowList.appendChild(row(
        fmtHour(d1.dt + i*3600, tz),
        d1.weather[0].description,
        d1.weather[0].main,
        `${convertTemp(d1.temp.day - i*0.1).toFixed(1)}${tempUnit()}`,
        `${convertSpeed(d1.speed).toFixed(1)} ${speedUnit()}`,
        `${d1.humidity}%`
      ));
    }

    data.list.forEach(day=>{
      tenDaysList.appendChild(row(
        fmtDay(day.dt, tz),
        day.weather[0].description,
        day.weather[0].main,
        `${convertTemp(day.temp.day).toFixed(1)}${tempUnit()}`,
        `${convertSpeed(day.speed).toFixed(1)} ${speedUnit()}`,
        `${day.humidity}%`
      ));
    });
  }catch(err){
    console.error(err);
    alert('Failed to load weather data.');
  }
}

function row(time,desc,main,temp,wind,humidity){
  const el=document.createElement('div');
  el.className='list-item';
  el.innerHTML=`<div class="li-left"><span class="wx-emoji">${iconFrom(main)}</span>
  <div><div class="li-time">${time}</div><div class="li-sub">${desc}</div></div></div>
  <div class="li-right"><div class="li-temp">${temp}</div><div>Wind: ${wind}</div><div>Humidity: ${humidity}</div></div>`;
  return el;
}

$('#searchForm').addEventListener('submit',e=>{
  e.preventDefault();
  const q=$('#searchInput').value.trim();
  if(q)load(q);
});

load($('#searchInput').value||'cesis,latvia');
